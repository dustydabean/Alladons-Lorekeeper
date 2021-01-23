<?php

namespace App\Http\Controllers\Admin\Users;

use Auth;
use Settings;
use Config;
use Illuminate\Http\Request;

use App\Models\User\User;
use App\Models\Item\Item;
use App\Models\Currency\Currency;

use App\Models\User\UserItem;
use App\Models\Character\CharacterItem;
use App\Models\Trade;
use App\Models\Character\CharacterDesignUpdate;
use App\Models\Submission\Submission;
use App\Models\User\UserCurrency;
use App\Models\SitePage;

use App\Models\Character\Character;
use App\Services\CurrencyManager;
use App\Services\InventoryManager;

use App\Http\Controllers\Controller;

class GrantController extends Controller
{
    /**
     * Show the currency grant page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUserCurrency()
    {
        return view('admin.grants.user_currency', [
            'users' => User::orderBy('id')->pluck('name', 'id'),
            'userCurrencies' => Currency::where('is_user_owned', 1)->orderBy('sort_user', 'DESC')->pluck('name', 'id')
        ]);
    }

    /**
     * Grants or removes currency from multiple users.
     *
     * @param  \Illuminate\Http\Request      $request
     * @param  App\Services\CurrencyManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postUserCurrency(Request $request, CurrencyManager $service)
    {
        $data = $request->only(['names', 'currency_id', 'quantity', 'data']);
        if($service->grantUserCurrencies($data, Auth::user())) {
            flash('Currency granted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Show the item grant page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getItems()
    {
        return view('admin.grants.items', [
            'users' => User::orderBy('id')->pluck('name', 'id'),
            'items' => Item::orderBy('name')->pluck('name', 'id')
        ]);
    }

    /**
     * Grants or removes items from multiple users.
     *
     * @param  \Illuminate\Http\Request        $request
     * @param  App\Services\InventoryManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postItems(Request $request, InventoryManager $service)
    {
        $data = $request->only(['names', 'item_ids', 'quantities', 'data', 'disallow_transfer', 'notes']);
        if($service->grantItems($data, Auth::user())) {
            flash('Items granted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Show the item search page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getItemSearch(Request $request)
    {
        $item = Item::find($request->only(['item_id']))->first();

        if($item) {
            // Gather all instances of this item
            $userItems = UserItem::where('item_id', $item->id)->where('count', '>', 0)->get();
            $characterItems = CharacterItem::where('item_id', $item->id)->where('count', '>', 0)->get();

            // Gather the users and characters that own them
            $users = User::whereIn('id', $userItems->pluck('user_id')->toArray())->orderBy('name', 'ASC')->get();
            $characters = Character::whereIn('id', $characterItems->pluck('character_id')->toArray())->orderBy('slug', 'ASC')->get();

            // Gather hold locations
            $designUpdates = CharacterDesignUpdate::whereIn('user_id', $userItems->pluck('user_id')->toArray())->whereNotNull('data')->get();
            $trades = Trade::whereIn('sender_id', $userItems->pluck('user_id')->toArray())->orWhereIn('recipient_id', $userItems->pluck('user_id')->toArray())->get();
            $submissions = Submission::whereIn('user_id', $userItems->pluck('user_id')->toArray())->whereNotNull('data')->get();
        }

        return view('admin.grants.item_search', [
            'item' => $item ? $item : null,
            'items' => Item::orderBy('name')->pluck('name', 'id'),
            'userItems' => $item ? $userItems : null,
            'characterItems' => $item ? $characterItems : null,
            'users' => $item ? $users : null,
            'characters' => $item ? $characters : null,
            'designUpdates' => $item ? $designUpdates :null,
            'trades' => $item ? $trades : null,
            'submissions' => $item ? $submissions : null,
        ]);
    }

    /**
     * Show the event currency info page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEventCurrency()
    {
        $total = UserCurrency::where('user_id', Settings::get('admin_user'))->where('currency_id', Settings::get('event_currency'))->first();

        return view('admin.grants.event_currency', [
            'currency' => Currency::find(Settings::get('event_currency')),
            'total' => $total,
            'progress' => $total ? ($total->quantity <= Settings::get('global_event_goal') ? ($total->quantity/Settings::get('global_event_goal'))*100 : 100) : 0,
            'inverseProgress' => $total ? ($total->quantity <= Settings::get('global_event_goal') ? 100-(($total->quantity/Settings::get('global_event_goal'))*100) : 0) : 100,
            'page' => SitePage::where('key', 'event-tracker')->first()
        ]);
    }

    /**
     * Show the clear event currency modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getClearEventCurrency()
    {
        return view('admin.grants._clear_event_currency', [
            'currency' => Currency::find(Settings::get('event_currency'))
        ]);
    }

    /**
     * Zeros event points for all users.
     *
     * @param  \Illuminate\Http\Request        $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postClearEventCurrency(Request $request)
    {
        if(UserCurrency::where('currency_id', Settings::get('event_currency'))->update(['quantity' => 0])) {
            flash('Event currency cleared successfully.')->success();
        }
        else {
            flash('Failed to clear event currency.')->error();
        }
        return redirect()->back();
    }

}
