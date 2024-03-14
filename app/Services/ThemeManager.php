<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;

use App\Models\Theme;
use App\Models\User\User;
use App\Models\User\UserTheme;

class ThemeManager extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Theme Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of theme.
    |
    */

    /**
     * Creates a new theme.
     *
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Theme\Theme
     */
    public function createTheme($data, $user)
    {
        DB::beginTransaction();

        try {

            $data['id'] = 0;
            $data = $this->populateData($data);

            $header = null;
            if(isset($data['header']) && $data['header']) {
                $data['has_header'] = 1;
                $header = $data['header'];
                unset($data['header']);
            }
            else $data['has_header'] = 0;

            $background = null;
            if(isset($data['background']) && $data['background']) {
                $data['has_background'] = 1;
                $background = $data['background'];
                unset($data['background']);
            }
            else $data['has_background'] = 0;

            $css = null;
            if(isset($data['css']) && $data['css']) {
                $data['has_css'] = 1;
                $css = $data['css'];
                unset($data['css']);
            }
            else $data['has_css'] = 0;

            $theme = Theme::create($data);
            if (!$themeEditor = (new ThemeEditorManager)->createTheme($data, $user)) throw new \Exception('Failed to create Theme Editor');
            $themeEditor->theme_id = $theme->id;
            $themeEditor->save();

            if ($header) {
                $theme->extension = $header->getClientOriginalExtension();
                $theme->update();
                $this->handleImage($header, $theme->imagePath, $theme->headerImageFileName, null);
            }
            if ($background) {
                $theme->extension_background = $background->getClientOriginalExtension();
                $theme->update();
                $this->handleImage($background, $theme->imagePath, $theme->backgroundImageFileName, null);
            }

            if ($css) $this->handleImage($css, $theme->imagePath, $theme->cssFileName, null);

            return $this->commitReturn($theme);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates an theme.
     *
     * @param  \App\Models\Theme\Theme  $theme
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Theme\Theme
     */
    public function updateTheme($theme, $data, $user)
    {
        DB::beginTransaction();

        try {

            // More specific validation
            if(Theme::where('name', $data['name'])->where('id', '!=', $theme->id)->exists()) throw new \Exception("The name has already been taken.");

            $data['id'] = $theme->id;
            $data = $this->populateData($data, $theme);

            $header = null;
            if(isset($data['header']) && $data['header']) {
                if (isset($theme->extension)) $old = $theme->headerImageFileName;
                else $old = null;
                $data['has_header'] = 1;
                $header = $data['header'];
                unset($data['header']);
            }
            $background = null;
            if(isset($data['background']) && $data['background']) {
                if (isset($theme->extension_background)) $old = $theme->backgroundImageFileName;
                else $old = null;
                $data['has_background'] = 1;
                $background = $data['background'];
                unset($data['background']);
            }

            $css = null;
            if(isset($data['css']) && $data['css']) {
                $data['has_css'] = 1;
                $css = $data['css'];
                unset($data['css']);
            }
            $theme->update($data);
            if ($theme->themeEditor) {
                $themeEditor = (new ThemeEditorManager)->updateTheme($theme->themeEditor, $data, $user);
            } else {
                if (!$themeEditor = (new ThemeEditorManager)->createTheme($data, $user)) throw new \Exception('Failed to create Theme Editor');
                $themeEditor->theme_id = $theme->id;
                $themeEditor->save();
            }
                
            if ($header) {
                $theme->extension = $header->getClientOriginalExtension();
                $theme->update();
                $this->handleImage($header, $theme->imagePath, $theme->headerImageFileName, $old);
            }
            if ($background) {
                $theme->extension_background = $background->getClientOriginalExtension();
                $theme->update();
                $this->handleImage($background, $theme->imagePath, $theme->backgroundImageFileName, $old);
            }

            if($css) $this->handleImage($css, $theme->imagePath, $theme->cssFileName);

            return $this->commitReturn($theme);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Processes user input for creating/updating an theme.
     *
     * @param  array                  $data
     * @param  \App\Models\Theme\Theme  $theme
     * @return array
     */
    private function populateData($data, $theme = null)
    {
        if(isset($data['description']) && $data['description']) $data['parsed_description'] = parse($data['description']);
        else $data['parsed_description'] = null;

        $data['prioritize_css'] = (isset($data['prioritize_css'])) ? 1 : 0;
        
        $data['hash'] = randomString(10);

        $names = explode(',',$data['creator_name']);
        $urls = explode(',',$data['creator_url']);
        $creators = [];

        if (count($names) != count($urls)) throw new \Exception("Creator name to creator url count mismatch.");
        foreach($names as $key => $creator)
        {
            $creators[trim($creator)] = trim($urls[$key]);
        }

        unset($data['creator_name']); unset($data['creator_url']);
        $data['creators'] = json_encode($creators);

        if(!isset($data['is_active'])) $data['is_active'] = 0;

        // Unset Default
        if(isset($data['is_default']))
        {
            DB::table('themes')
            ->where('id', '!=', $data['id'])
            ->where('is_default', 1)
            ->update(['is_default' => 0]);
        }
        else $data['is_default'] = 0;

        // Remove Header
        if(isset($data['remove_header']) && isset($theme->extension) && $data['remove_header'])
        {
            $data['extension'] = null;
            $this->deleteImage($theme->imagePath, $theme->headerImageFileName);
            unset($data['remove_image']);
            $data['has_header'] = 0;
        }

        // Remove Background
        if(isset($data['remove_background']) && isset($theme->extension_background) && $data['remove_background'])
        {
            $data['extension_background'] = null;
            $this->deleteImage($theme->imagePath, $theme->backgroundImageFileName);
            unset($data['remove_image']);
            $data['has_background'] = 0;
        }

        // Remove Css
        if(isset($data['remove_css']) && $data['remove_css'])
        {
            $this->deleteImage($theme->imagePath, $theme->cssFileName);
            unset($data['remove_css']);
            $data['has_css'] = 0;
        }

        if (isset($data['season_link_id'])) {
            $data['link_id'] = $data['season_link_id'];
            $data['link_type'] = 'season';
        } else if (isset($data['weather_link_id'])) {
            $data['link_id'] = $data['weather_link_id'];
            $data['link_type'] = 'weather';
        } 

        return $data;
    }

    /**
     * Deletes an theme.
     *
     * @param  \App\Models\Theme\Theme  $theme
     * @return bool
     */
    public function deleteTheme($theme)
    {
        DB::beginTransaction();

        try {
            foreach(User::where('theme_id',$theme->id) as $user)
            {
                $user->update(['theme_id' => null]);
            }

            if($theme->has_header) $this->deleteImage($theme->imagePath, $theme->headerImageFileName);
            if($theme->has_background) $this->deleteImage($theme->imagePath, $theme->backgroundImageFileName);
            if($theme->has_css) $this->deleteImage($theme->imagePath, $theme->cssFileName);
            $theme->themeEditor->delete();
            $theme->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Credits theme to a user or character.
     *
     * @param  \App\Models\User\User                        $sender
     * @param  \App\Models\User\User                        $recipient
     * @param  \App\Models\Character\Character              $character
     * @param  string                                       $type 
     * @param  string                                       $data
     * @param  \App\Models\Theme                            $theme
     * @param  int                                          $quantity
     * @return  bool
     */
    public function creditTheme($recipient, $theme) {
        DB::beginTransaction();

        try {
            if (is_numeric($theme)) $theme = Theme::find($theme);

            if ($recipient->themes->contains($theme)) {
                flash($recipient->name . " already has the theme " . $theme->displayName, 'warning');
                return $this->commitReturn(false);
            }

            $record = UserTheme::where('user_id', $recipient->id)->where('theme_id', $theme->id)->first();
            if ($record) {
                // Laravel doesn't support composite primary keys, so directly updating the DB row here
                DB::table('user_themes')->where('user_id', $recipient->id)->where('theme_id', $theme->id);
            } else {
                $record = UserTheme::create(['user_id' => $recipient->id, 'theme_id' => $theme->id]);
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }


}
