<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;

use App\Models\SitePage;

class PageService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Page Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of site pages.
    |
    */

    /**
     * Creates a site page.
     *
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\SitePage
     */
    public function createPage($data, $user)
    {
        DB::beginTransaction();

        try {
            if(isset($data['text']) && $data['text']) $data['parsed_text'] = parse($data['text']);
            $data['user_id'] = $user->id;
            if(!isset($data['is_visible'])) $data['is_visible']     = 0;
            if(!isset($data['admin_only'])) $data['admin_only']     = 0;
            if(!isset($data['can_comment'])) $data['can_comment']   = 0;

            $page = SitePage::create($data);

            return $this->commitReturn($page);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates a site page.
     *
     * @param  \App\Models\SitePage   $news
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\SitePage
     */
    public function updatePage($page, $data, $user)
    {
        DB::beginTransaction();

        try {
            // More specific validation
            if(SitePage::where('key', $data['key'])->where('id', '!=', $page->id)->exists()) throw new \Exception("The key has already been taken.");

            if(isset($data['text']) && $data['text']) $data['parsed_text'] = parse($data['text']);
            $data['user_id'] = $user->id;
            if(!isset($data['is_visible'])) $data['is_visible']     = 0;
            if(!isset($data['admin_only'])) $data['admin_only']     = 0;
            if(!isset($data['can_comment'])) $data['can_comment']   = 0;

            $page->update($data);

            return $this->commitReturn($page);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a site page.
     *
     * @param  \App\Models\SitePage  $news
     * @return bool
     */
    public function deletePage($page)
    {
        DB::beginTransaction();

        try {
            // Specific pages such as the TOS/privacy policy cannot be deleted from the admin panel.
            if(Config::get('lorekeeper.text_pages.'.$page->key)) throw new \Exception("You cannot delete this page.");

            $page->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
}
