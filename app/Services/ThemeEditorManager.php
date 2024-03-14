<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;

use App\Models\ThemeEditor;

class ThemeEditorManager extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Theme Editor Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of themes.
    |
    */

    /**
     * Creates a theme.
     *
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\ThemeEditor
     */
    public function createTheme($data, $user)
    {
        DB::beginTransaction();

        try {
            $data['header_image_display'] = (isset($data['header_image_display'])) ?  'inline' : 'none'; 
            $data['background_size'] = (isset($data['background_size'])) ?  'cover' : 'auto'; 
            $data['header_image_url'] = (isset($data['header_image_url'])) ? $data['header_image_url'] : '';
            $data['background_image_url'] = (isset($data['background_image_url'])) ? $data['background_image_url'] : '';

            $theme = ThemeEditor::create($data);

            return $this->commitReturn($theme);
        } catch (\Exception $e) {
            dd($e->getMessage());
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates a theme.
     *
     * @param  \App\Models\SitePage   $news
     * @param  array                  $data 
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\SitePage
     */
    public function updateTheme($theme, $data, $user)
    {
        DB::beginTransaction();

        try {
            $data['header_image_display'] = (isset($data['header_image_display'])) ?  'inline' : 'none'; 
            $data['background_size'] = (isset($data['background_size'])) ?  'cover' : 'auto'; 
            $data['header_image_url'] = (isset($data['header_image_url'])) ? $data['header_image_url'] : ''; 
            $data['background_image_url'] = (isset($data['background_image_url'])) ? $data['background_image_url'] : ''; 
            $data['is_released'] = (isset($data['is_released'])) ? 1 : 0;

            $theme->update($data);

            return $this->commitReturn($theme);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a theme.
     *
     * @param  \App\Models\SitePage  $news
     * @return bool
     */
    public function deleteTheme($theme)
    {
        DB::beginTransaction();

        try {

            $theme->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
}