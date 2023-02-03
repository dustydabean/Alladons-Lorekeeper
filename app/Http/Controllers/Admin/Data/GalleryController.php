<?php

namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;

use Auth;

use App\Models\Gallery\Gallery;
use App\Services\GalleryService;

use App\Http\Controllers\Controller;

class GalleryController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Admin / Gallery Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of galleries.
    |
    */

    /**
     * Shows the gallery index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('admin.galleries.galleries', [
            'galleries' => Gallery::sort()->whereNull('parent_id')->paginate(10)
        ]);
    }

    /**
     * Shows the create gallery page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateGallery()
    {
        return view('admin.galleries.create_edit_gallery', [
            'gallery' => new Gallery,
            'galleries' => Gallery::sort()->pluck('name','id')
        ]);
    }

    /**
     * Shows the edit gallery page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditGallery($id)
    {
        $gallery = Gallery::find($id);
        if(!$gallery) abort(404);
        return view('admin.galleries.create_edit_gallery', [
            'gallery' => $gallery,
            'galleries' => Gallery::sort()->pluck('name','id')->forget($id)
        ]);
    }

    /**
     * Creates or edits a gallery.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  App\Services\GalleryService $service
     * @param  int|null                    $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditGallery(Request $request, GalleryService $service, $id = null)
    {
        $id ? $request->validate(Gallery::$updateRules) : $request->validate(Gallery::$createRules);
        $data = $request->only([
            'name', 'sort', 'parent_id', 'description', 'submissions_open', 'currency_enabled', 'votes_required', 'start_at', 'end_at', 'hide_before_start', 'prompt_selection'
        ]);
        if($id && $service->updateGallery(Gallery::find($id), $data, Auth::user())) {
            flash('Gallery updated successfully.')->success();
        }
        else if (!$id && $gallery = $service->createGallery($data, Auth::user())) {
            flash('Gallery created successfully.')->success();
            return redirect()->to('admin/data/galleries/edit/'.$gallery->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Gets the gallery deletion modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteGallery($id)
    {
        $gallery = Gallery::find($id);
        return view('admin.galleries._delete_gallery', [
            'gallery' => $gallery,
        ]);
    }

    /**
     * Deletes a gallery.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  App\Services\GalleryService  $service
     * @param  int                         $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteGallery(Request $request, GalleryService $service, $id)
    {
        if($id && $service->deleteGallery(Gallery::find($id))) {
            flash('Gallery deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/data/galleries');
    }
}
