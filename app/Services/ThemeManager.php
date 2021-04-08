<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;

use App\Models\Theme;

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

            $data = $this->populateData($data);

            $header = null;
            if(isset($data['image']) && $data['image']) {
                $data['has_header'] = 1;
                $header = $data['image'];
                unset($data['image']);
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
                $this->handleImage($header, $theme->path, $theme->imageFileName, null);
                $this->processImage($header);
            }
            if ($css) {
                $this->handleImage($css, $theme->path, $theme->cssFileName, null);
                $this->processImage($css);
            }

            $this->processImage($image);


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
            if(isset($data['theme_category_id']) && $data['theme_category_id'] == 'none') $data['theme_category_id'] = null;

            // More specific validation
            if(Theme::where('name', $data['name'])->where('id', '!=', $theme->id)->exists()) throw new \Exception("The name has already been taken.");
            if((isset($data['theme_category_id']) && $data['theme_category_id']) && !ThemeCategory::where('id', $data['theme_category_id'])->exists()) throw new \Exception("The selected theme category is invalid.");

            $data = $this->populateData($data);

            $header = null;
            if(isset($data['header']) && $data['header']) {
                $data['has_header'] = 1;
                $header = $data['header'];
                unset($data['header']);
            }
            $css = null;
            if(isset($data['css']) && $data['css']) {
                $data['has_css'] = 1;
                $header = $data['css'];
                unset($data['css']);
            }

            $theme->update($data);

            $theme->update([
                'data' => json_encode([
                    'rarity' => isset($data['rarity']) && $data['rarity'] ? $data['rarity'] : null,
                    'uses' => isset($data['uses']) && $data['uses'] ? $data['uses'] : null,
                    'release' => isset($data['release']) && $data['release'] ? $data['release'] : null,
                    'prompts' => isset($data['prompts']) && $data['prompts'] ? $data['prompts'] : null,
                    'resell' => isset($data['currency_quantity']) ? [$data['currency_id'] => $data['currency_quantity']] : null,
                    ])
            ]);

            if ($theme) $this->handleImage($header, $theme->headerPath, $theme->headerFileName);

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

        if (count($data['creator_name']) != count($data['creator_url'])) throw new \Exception("Creator name to creator url count mismatch.");
        foreach($data['creator_name'] as $creator)
        {
            $creators[$creator] = $data['creator_url'];
        }
        unset($data['creator_name']); unset($data['creator_url']);
        $data['creators'] = json_encode($creators);

        if(!isset($data['is_active'])) $data['is_active'] = 0;

        if(isset($data['is_default']))
        {
            $oldDefault = Theme::where('is_default',1)->first();
            if ($oldDefault) $oldDefault->update(['is_default',0]);
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
            // Check first if the theme is currently owned or if some other site feature uses it
            if(DB::table('user_themes')->where([['theme_id', '=', $theme->id], ['count', '>', 0]])->exists()) throw new \Exception("At least one user currently owns this theme. Please remove the theme(s) before deleting it.");
            if(DB::table('character_themes')->where([['theme_id', '=', $theme->id], ['count', '>', 0]])->exists()) throw new \Exception("At least one character currently owns this theme. Please remove the theme(s) before deleting it.");
            if(DB::table('loots')->where('rewardable_type', 'Theme')->where('rewardable_id', $theme->id)->exists()) throw new \Exception("A loot table currently distributes this theme as a potential reward. Please remove the theme before deleting it.");
            if(DB::table('prompt_rewards')->where('rewardable_type', 'Theme')->where('rewardable_id', $theme->id)->exists()) throw new \Exception("A prompt currently distributes this theme as a reward. Please remove the theme before deleting it.");
            if(DB::table('shop_stock')->where('theme_id', $theme->id)->exists()) throw new \Exception("A shop currently stocks this theme. Please remove the theme before deleting it.");

            DB::table('themes_log')->where('theme_id', $theme->id)->delete();
            DB::table('user_themes')->where('theme_id', $theme->id)->delete();
            DB::table('character_themes')->where('theme_id', $theme->id)->delete();
            $theme->tags()->delete();
            if($theme->has_header) $this->deleteImage($theme->headerPath, $theme->headerFileName);
            $theme->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }


}
