<?php

namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;

use Auth;

use App\Models\Item\Item;
use App\Models\Item\ItemCategory;
use App\Models\Loot\LootTable;
use App\Models\Raffle\Raffle;
use App\Models\Currency\Currency;
use App\Models\Collection\Collection;

use App\Services\CollectionService;
use App\Models\Collection\CollectionCategory;

use App\Http\Controllers\Controller;


class CollectionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Admin / Collection Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of collections.
    |
    */

    /**********************************************************************************************
    
        COLLECTIONS
    **********************************************************************************************/

    /**
     * Shows the collection index.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCollectionIndex(Request $request)
    {
        $query = Collection::query();
        $data = $request->only(['name', 'collection_category_id', 'is_visible']);
        if(isset($data['collection_category_id']) && $data['collection_category_id'] != 'none')
            $query->where('collection_category_id', $data['collection_category_id']);
        if(isset($data['is_visible']) && $data['is_visible'] != 'none') 
            $query->where('is_visible', $data['is_visible']);
        if(isset($data['name'])) 
            $query->where('name', 'LIKE', '%'.$data['name'].'%');
        return view('admin.collections.collections', [
            'collections' => $query->paginate(20)->appends($request->query()),
            'categories' => ['none' => 'Any Category'] + CollectionCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'is_visible' => ['none' => 'Any Status', '0' => 'Unreleased', '1' => 'Released'],
        ]);
    }

    /**
     * Shows the create collection page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateCollection()
    {
        return view('admin.collections.create_edit_collection', [
            'collection' => new Collection,
            'items' => Item::orderBy('name')->pluck('name', 'id'),
            'categories' => ItemCategory::orderBy('name')->pluck('name', 'id'),
            'currencies' => Currency::where('is_user_owned', 1)->orderBy('name')->pluck('name', 'id'),
            'tables' => LootTable::orderBy('name')->pluck('name', 'id'),
            'raffles' => Raffle::where('rolled_at', null)->where('is_active', 1)->orderBy('name')->pluck('name', 'id'),
            'collections' => ['none' => 'No parent'] + Collection::visible()->pluck('name', 'id')->toArray(),
            'collectioncategories' => ['none' => 'No category'] + CollectionCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Shows the edit collection page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditCollection($id)
    {
        $collection = Collection::find($id);
        if(!$collection) abort(404);
        return view('admin.collections.create_edit_collection', [
            'collection' => $collection,
            'items' => Item::orderBy('name')->pluck('name', 'id'),
            'categories' => ItemCategory::orderBy('name')->pluck('name', 'id'),
            'currencies' => Currency::where('is_user_owned', 1)->orderBy('name')->pluck('name', 'id'),
            'tables' => LootTable::orderBy('name')->pluck('name', 'id'),
            'raffles' => Raffle::where('rolled_at', null)->where('is_active', 1)->orderBy('name')->pluck('name', 'id'),
            'collections' => ['none' => 'No parent'] + Collection::visible()->where('id', '!=', $collection->id)->pluck('name', 'id')->toArray(),
            'collectioncategories' => ['none' => 'No category'] + CollectionCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Creates or edits an collection.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\CollectionService  $service
     * @param  int|null                  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditCollection(Request $request, CollectionService $service, $id = null)
    {
        $id ? $request->validate(Collection::$updateRules) : $request->validate(Collection::$createRules);
        $data = $request->only([
            'name', 'description', 'image', 'remove_image', 'is_visible',
            'ingredient_type', 'ingredient_data', 'ingredient_quantity',
            'rewardable_type', 'rewardable_id', 'reward_quantity', 'collection_category_id',
            'parent_id'
        ]);
        if($id && $service->updateCollection(Collection::find($id), $data, Auth::user())) {
            flash('Collection updated successfully.')->success();
        }
        else if (!$id && $collection = $service->createCollection($data, Auth::user())) {
            flash('Collection created successfully.')->success();
            return redirect()->to('admin/data/collections/edit/'.$collection->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Gets the collection deletion modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteCollection($id)
    {
        $collection = Collection::find($id);
        return view('admin.collections._delete_collection', [
            'collection' => $collection,
        ]);
    }

    /**
     * Creates or edits an collection.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\CollectionService  $service
     * @param  int                       $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteCollection(Request $request, CollectionService $service, $id)
    {
        if($id && $service->deleteCollection(Collection::find($id))) {
            flash('Collection deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/data/collections');
    }


     /**********************************************************************************************
        Collection CATEGORIES
    **********************************************************************************************/

    /**
     * Shows the collection category index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCollectionCategoryIndex()
    {
        return view('admin.collections.collection_categories', [
            'categories' => CollectionCategory::orderBy('sort', 'DESC')->get(),
        ]);
    }

    /**
     * Shows the create collection category page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateCollectionCategory()
    {
        return view('admin.collections.create_edit_collection_category', [
            'category' => new CollectionCategory
        ]);
    }

    /**
     * Shows the edit collection category page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditCollectionCategory($id)
    {
        $category = CollectionCategory::find($id);
        if(!$category) abort(404);
        return view('admin.collections.create_edit_collection_category', [
            'category' => $category
        ]);
    }

    /**
     * Creates or edits an collection category.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\CollectionService  $service
     * @param  int|null                  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditCollectionCategory(Request $request, CollectionService $service, $id = null)
    {
        $id ? $request->validate(CollectionCategory::$updateRules) : $request->validate(CollectionCategory::$createRules);
        $data = $request->only([
            'name', 'description', 'image', 'remove_image'
        ]);
        if($id && $service->updateCollectionCategory(CollectionCategory::find($id), $data, Auth::user())) {
            flash('Category updated successfully.')->success();
        }
        else if (!$id && $category = $service->createCollectionCategory($data, Auth::user())) {
            flash('Category created successfully.')->success();
            return redirect()->to('admin/data/collections/collection-categories/edit/'.$category->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Gets the collection category deletion modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteCollectionCategory($id)
    {
        $category = CollectionCategory::find($id);
        return view('admin.collections._delete_collection_category', [
            'category' => $category,
        ]);
    }

    /**
     * Deletes an collection category.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\CollectionService  $service
     * @param  int                       $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteCollectionCategory(Request $request, CollectionService $service, $id)
    {
        if($id && $service->deleteCollectionCategory(CollectionCategory::find($id))) {
            flash('Category deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/data/collections/collection-categories');
    }

    /**
     * Sorts collection categories.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\CollectionService  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSortCollectionCategory(Request $request, CollectionService $service)
    {
        if($service->sortCollectionCategory($request->get('sort'))) {
            flash('Category order updated successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

}