<?php

namespace App\Http\Controllers\Users;

use DB;
use Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Collection\Collection;
use App\Models\Collection\CollectionCategory;
use App\Models\User\UserCollection;
use App\Models\Item\ItemCategory;
use App\Models\Item\Item;
use App\Models\User\User;
use App\Models\User\UserItem;
use App\Models\Currency\Currency;

use App\Services\CollectionService;
use App\Services\CollectionManager;
class CollectionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Collection Controller
    |--------------------------------------------------------------------------
    |
    | Handles viewing collections, as well as their usage.
    |
    */

    /**
     * Shows the user's trades.
     *
     * @param  string  $type
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex(Request $request)
    { $incompleted = Auth::user()->incompletedCollections;
        $categories = CollectionCategory::orderBy('sort', 'DESC')->get();
        $collections = count($categories) ? Auth::user()->collections()->orderByRaw('FIELD(collection_category_id,'.implode(',', $categories->pluck('id')->toArray()).')')->orderBy('name')->get()->groupBy('collection_category_id') : Auth::user()->collections()->orderBy('name')->get()->groupBy('collection_category_id');
        $incomplete = count($categories) ? $incompleted->orderByRaw('FIELD(collection_category_id,'.implode(',', $categories->pluck('id')->toArray()).')')->orderBy('name')->get()->groupBy('collection_category_id') : $incompleted->orderBy('name')->get()->groupBy('collection_category_id');


        return view('home.collection.index', [
            'categories' => $categories->keyBy('id'),
            'collections' => $collections,
            'incomplete' => $incomplete,
        ]);
    }

    /**
     * Shows a collection's modal.
     *
     * @param  integer  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCompleteCollection(CollectionManager $service, $id)
    {
        $collection = Collection::find($id);
        $selected = [];

        if(!$collection || !Auth::user()) abort(404);

        // foreach ingredient, search for a qualifying item in the users inv, and select items up to the quantity, if insufficient continue onto the next entry
        // until there are no more eligible items, then proceed to the next item
        $selected = $service->pluckIngredients(Auth::user(), $collection);

        $inventory = UserItem::with('item')->whereNull('deleted_at')->where('count', '>', '0')->where('user_id', Auth::user()->id)->get();

        return view('home.collection._modal_collection', [
            'collection' => $collection,
            'categories' => ItemCategory::orderBy('sort', 'DESC')->get(),
            'item_filter' => Item::orderBy('name')->get()->keyBy('id'),
            'inventory' => $inventory,
            'page' => 'collection',
            'selected' => $selected
        ]);
    }

    /**
     * Completes a collection
     *
     * @param  integer  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function postCompleteCollection(Request $request, CollectionManager $service, $id)
    {
        $collection = Collection::find($id);
        if(!$collection) abort(404);

        if($service->completeCollection($request->only(['stack_id', 'stack_quantity']), $collection, Auth::user())) {
            flash('Collection completed successfully. Congratulations!')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

}