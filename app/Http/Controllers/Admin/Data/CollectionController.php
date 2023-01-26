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
        $data = $request->only(['name']);
        if(isset($data['name'])) 
            $query->where('name', 'LIKE', '%'.$data['name'].'%');
        return view('admin.collections.collections', [
            'collections' => $query->paginate(20)->appends($request->query())
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
            'collections'=> Collection::orderBy('name')->pluck('name', 'id'),
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
            'collections'=> Collection::orderBy('name')->pluck('name', 'id'),
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
            'rewardable_type', 'rewardable_id', 'reward_quantity'
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


}