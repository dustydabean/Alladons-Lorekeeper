<?php

namespace App\Http\Controllers\Users;

use Auth;
use Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Trade;
use App\Models\TradeListing;
use App\Models\Item\ItemCategory;
use App\Models\Item\Item;
use App\Models\User\User;
use App\Models\User\UserItem;
use App\Models\Currency\Currency;
use App\Models\Character\CharacterCategory;

use App\Services\TradeManager;
use App\Services\TradeListingManager;

class TradeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Trade Controller
    |--------------------------------------------------------------------------
    |
    | Handles viewing the user's trade index, creating and acting on trades.
    |
    */

    /**
     * Shows the user's trades.
     *
     * @param  string  $type
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex($status = 'open')
    {
        $user = Auth::user();
        $trades = Trade::with('recipient')->with('sender')->with('staff')->where(function($query) {
            $query->where('recipient_id', Auth::user()->id)->orWhere('sender_id', Auth::user()->id);
        })->where('status', ucfirst($status))->orderBy('id', 'DESC');

        $stacks = array();
        foreach($trades->get() as $trade) {
            foreach($trade->data as $side=>$assets) {
                if(isset($assets['user_items'])) {
                    $user_items = UserItem::with('item')->find(array_keys($assets['user_items']));
                    $items = array();
                    foreach($assets['user_items'] as $id=>$quantity) {
                        $user_item = $user_items->find($id);
                        $user_item['quantity'] = $quantity;
                        array_push($items,$user_item);
                    }
                    $items = collect($items)->groupBy('item_id');
                    $stacks[$trade->id][$side] = $items;
                }
            }
        }

        return view('home.trades.index', [
            'trades' => $trades->paginate(20),
            'stacks' => $stacks
        ]);
    }

    /**
     * Shows a trade.
     *
     * @param  integer  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getTrade($id)
    {
        $trade = Trade::find($id);
        
        if($trade->status != 'Completed' && !Auth::user()->hasPower('manage_characters') && !($trade->sender_id == Auth::user()->id || $trade->recipient_id == Auth::user()->id))   $trade = null;

        if(!$trade) abort(404);
        return view('home.trades.trade', [
            'trade' => $trade,
            'partner' => (Auth::user()->id == $trade->sender_id) ? $trade->recipient : $trade->sender,
            'senderData' => isset($trade->data['sender']) ? parseAssetData($trade->data['sender']) : null,
            'recipientData' => isset($trade->data['recipient']) ? parseAssetData($trade->data['recipient']) : null,
            'items' => Item::all()->keyBy('id')
        ]);
    }

    /**
     * Shows the trade creation page.
     *
     * @param  integer  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateTrade()
    {
        $inventory = UserItem::with('item')->whereNull('deleted_at')->where('count', '>', '0')->where('user_id', Auth::user()->id)
        ->get()
        ->filter(function($userItem){
            return $userItem->isTransferrable == true;
        })
        ->sortBy('item.name');;
        return view('home.trades.create_trade', [
            'categories' => ItemCategory::orderBy('sort', 'DESC')->get(),
            'item_filter' => Item::orderBy('name')->get()->keyBy('id'),
            'inventory' => $inventory,
            'userOptions' => User::visible()->where('id', '!=', Auth::user()->id)->orderBy('name')->pluck('name', 'id')->toArray(),
            'characters' => Auth::user()->allCharacters()->visible()->tradable()->with('designUpdate')->get(),
            'characterCategories' => CharacterCategory::orderBy('sort', 'DESC')->get(),
            'page' => 'trade'
        ]);
    }

    /**
     * Shows the trade edit page.
     *
     * @param  integer  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditTrade($id)
    {
        $trade = Trade::where('id', $id)->where(function($query) {
            $query->where('recipient_id', Auth::user()->id)->orWhere('sender_id', Auth::user()->id);
        })->where('status', 'Open')->first();

        if($trade)
            $inventory = UserItem::with('item')->whereNull('deleted_at')->where('count', '>', '0')->where('user_id', Auth::user()->id)
            ->get()
            ->filter(function($userItem){
                return $userItem->isTransferrable == true;
            })
            ->sortBy('item.name');
        else $trade = null;
        return view('home.trades.edit_trade', [
            'trade' => $trade,
            'partner' => (Auth::user()->id == $trade->sender_id) ? $trade->recipient : $trade->sender,
            'categories' => ItemCategory::orderBy('sort', 'DESC')->get(),
            'item_filter' => Item::orderBy('name')->get()->keyBy('id'),
            'inventory' => $inventory,
            'userOptions' => User::visible()->orderBy('name')->pluck('name', 'id')->toArray(),
            'characters' => Auth::user()->allCharacters()->visible()->with('designUpdate')->get(),
            'characterCategories' => CharacterCategory::orderBy('sort', 'DESC')->get(),
            'page' => 'trade'
        ]);
    }
    
    /**
     * Creates a new trade.
     *
     * @param  \Illuminate\Http\Request   $request
     * @param  App\Services\TradeManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateTrade(Request $request, TradeManager $service)
    {
        if($trade = $service->createTrade($request->only(['recipient_id', 'comments', 'stack_id', 'stack_quantity', 'currency_id', 'currency_quantity', 'character_id', 'terms_link']), Auth::user())) {
            flash('Trade created successfully.')->success();
            return redirect()->to($trade->url);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    /**
     * Edits a trade.
     *
     * @param  \Illuminate\Http\Request   $request
     * @param  App\Services\TradeManager  $service
     * @param  integer  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEditTrade(Request $request, TradeManager $service, $id)
    {
        if($trade = $service->editTrade($request->only(['comments', 'stack_id', 'stack_quantity', 'currency_id', 'currency_quantity', 'character_id']) + ['id' => $id], Auth::user())) {
            flash('Trade offer edited successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Shows the offer confirmation modal.
     *
     * @param  integer  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getConfirmOffer($id)
    {
        $trade = Trade::where('id', $id)->where(function($query) {
            $query->where('recipient_id', Auth::user()->id)->orWhere('sender_id', Auth::user()->id);
        })->where('status', 'Open')->first();
        
        return view('home.trades._confirm_offer_modal', [
            'trade' => $trade
        ]);
    }
    
    /**
     * Confirms or unconfirms an offer.
     *
     * @param  \Illuminate\Http\Request   $request
     * @param  App\Services\TradeManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postConfirmOffer(Request $request, TradeManager $service, $id)
    {
        if($trade = $service->confirmOffer(['id' => $id], Auth::user())) {
            flash('Trade offer confirmation edited successfully.')->success();
            return redirect()->back();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Shows the trade confirmation modal.
     *
     * @param  integer  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getConfirmTrade($id)
    {
        $trade = Trade::where('id', $id)->where(function($query) {
            $query->where('recipient_id', Auth::user()->id)->orWhere('sender_id', Auth::user()->id);
        })->where('status', 'Open')->first();
        
        return view('home.trades._confirm_trade_modal', [
            'trade' => $trade
        ]);
    }
    
    /**
     * Confirms or unconfirms a trade.
     *
     * @param  \Illuminate\Http\Request   $request
     * @param  App\Services\TradeManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postConfirmTrade(Request $request, TradeManager $service, $id)
    {
        if($trade = $service->confirmTrade(['id' => $id], Auth::user())) {
            flash('Trade confirmed successfully.')->success();
            return redirect()->back();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Shows the trade cancellation modal.
     *
     * @param  integer  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCancelTrade($id)
    {
        $trade = Trade::where('id', $id)->where(function($query) {
            $query->where('recipient_id', Auth::user()->id)->orWhere('sender_id', Auth::user()->id);
        })->where('status', 'Open')->first();
        
        return view('home.trades._cancel_trade_modal', [
            'trade' => $trade
        ]);
    }
    
    /**
     * Cancels a trade.
     *
     * @param  \Illuminate\Http\Request   $request
     * @param  App\Services\TradeManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCancelTrade(Request $request, TradeManager $service, $id)
    {
        if($trade = $service->cancelTrade(['id' => $id], Auth::user())) {
            flash('Trade canceled successfully.')->success();
            return redirect()->back();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**********************************************************************************************
    
        TRADE LISTINGS

    **********************************************************************************************/

    /**
     * Shows the trade listing index.
     *
     * @param  string  $type
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getListingIndex(Request $request)
    {
        return view('home.trades.listings.index', [
            'listings' => TradeListing::active()->orderBy('id', 'DESC')->paginate(10),
            'listingDuration' => Settings::get('trade_listing_duration'),
        ]);
    }

    /**
     * Shows the user's expired trade listings.
     *
     * @param  string  $type
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getExpiredListings(Request $request)
    {
        return view('home.trades.listings.expired', [
            'listings' => TradeListing::expired()->where('user_id', Auth::user()->id)->orderBy('id', 'DESC')->paginate(10),
            'listingDuration' => Settings::get('trade_listing_duration'),
        ]);
    }

    /**
     * Shows a trade.
     *
     * @param  integer  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getListing($id)
    {
        $listing = TradeListing::find($id);
        if(!$listing) abort(404);

        return view('home.trades.listings.view_listing', [
            'listing' => $listing,
            'seekingData' => isset($listing->data['seeking']) ? parseAssetData($listing->data['seeking']) : null,
            'offeringData' => isset($listing->data['offering']) ? parseAssetData($listing->data['offering']) : null,
            'items' => Item::all()->keyBy('id')
        ]);
    }

    /**
     * Shows the create trade listing page.
     *
     * @param  string  $type
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateListing(Request $request)
    {
        $inventory = UserItem::with('item')->whereNull('deleted_at')->where('count', '>', '0')->where('user_id', Auth::user()->id)
        ->get()
        ->filter(function($userItem){
            return $userItem->isTransferrable == true;
        })
        ->sortBy('item.name');;
        $currencies = Currency::where('is_user_owned', 1)->where('allow_user_to_user', 1)->orderBy('sort_user', 'DESC')->get();

        return view('home.trades.listings.create_listing', [
            'items' => Item::orderBy('name')->where('allow_transfer', 1)->pluck('name', 'id'),
            'currencies' => $currencies,
            'categories' => ItemCategory::orderBy('sort', 'DESC')->get(),
            'item_filter' => Item::orderBy('name')->get()->keyBy('id'),
            'inventory' => $inventory,
            'characters' => Auth::user()->allCharacters()->visible()->tradable()->with('designUpdate')->get(),
            'characterCategories' => CharacterCategory::orderBy('sort', 'DESC')->get(),
            'page' => 'listing',
            'listingDuration' => Settings::get('trade_listing_duration')
        ]);
    }

    /**
     * Creates a new trade listing.
     *
     * @param  \Illuminate\Http\Request          $request
     * @param  App\Services\TradeListingManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateListing(Request $request, TradeListingManager $service)
    {
        if($listing = $service->createTradeListing($request->only(['comments', 'contact', 'item_ids', 'quantities', 'stack_id', 'stack_quantity', 'offer_currency_ids', 'seeking_currency_ids', 'character_id', 'offering_etc', 'seeking_etc']), Auth::user())) {
            flash('Trade listing created successfully.')->success();
            return redirect()->to($listing->url);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Manually marks a trade listing as expired.
     *
     * @param  \Illuminate\Http\Request          $request
     * @param  App\Services\TradeListingManager  $service
     * @param  integer  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postExpireListing(Request $request, TradeListingManager $service, $id)
    {
        $listing = TradeListing::find($id);
        if(!$listing) abort(404);
        
        if($service->markExpired(['id' => $id], Auth::user())) {
            flash('Listing expired successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
}


