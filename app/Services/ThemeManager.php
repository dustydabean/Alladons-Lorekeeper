<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;

use App\Models\Theme;
use App\Models\User\User;

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

            $css = null;
            if(isset($data['css']) && $data['css']) {
                $data['has_css'] = 1;
                $css = $data['css'];
                unset($data['css']);
            }
            else $data['has_css'] = 0;

            $theme = Theme::create($data);

            if ($header) {
                $theme->extension = $header->getClientOriginalExtension();
                $theme->update();
                $this->handleImage($header, $theme->imagePath, $theme->imageFileName, null);
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
                if(isset($category->extension)) $old = $category->imageFileName;
                else $old = null;
                $data['has_header'] = 1;
                $header = $data['header'];
                unset($data['header']);
            }
            $css = null;
            if(isset($data['css']) && $data['css']) {
                $data['has_css'] = 1;
                $css = $data['css'];
                unset($data['css']);
            }
            $theme->update($data);

            if ($header) {
                $theme->extension = $header->getClientOriginalExtension();
                $theme->update();
                $this->handleImage($header, $theme->imagePath, $theme->imageFileName, $old);
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
            $this->deleteImage($theme->imagePath, $theme->imageFileName);
            unset($data['remove_image']);
            $data['has_header'] = 0;
        }

        // Remove Css
        if(isset($data['remove_css']) && $data['remove_css'])
        {
            $this->deleteImage($theme->imagePath, $theme->cssFileName);
            unset($data['remove_css']);
            $data['has_css'] = 0;
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

            if($theme->has_header) $this->deleteImage($theme->imagePath, $theme->imageFileName);
            if($theme->has_css) $this->deleteImage($theme->imagePath, $theme->cssFileName);
            $theme->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }


}
