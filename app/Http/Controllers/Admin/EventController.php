<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Facades\Settings;
use Illuminate\Http\Request;
use App\Models\Currency\Currency;
use App\Models\User\UserCurrency;
use App\Models\SitePage;
use App\Http\Controllers\Controller;
use App\Models\EventTeam;
use App\Services\EventService;

class EventController extends Controller
{
    /**
     * Show the event settings page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEventSettings()
    {
        $total = UserCurrency::where('user_id', Settings::get('admin_user'))->where('currency_id', Settings::get('event_currency'))->first();

        return view('admin.events.event_currency', [
            'currency' => Currency::find(Settings::get('event_currency')),
            'total' => $total,
            'progress' => $total ? ($total->quantity < Settings::get('event_global_goal') ? ($total->quantity/Settings::get('event_global_goal'))*100 : 100) : 0,
            'inverseProgress' => $total ? ($total->quantity < Settings::get('event_global_goal') ? 100-(($total->quantity/Settings::get('event_global_goal'))*100) : 0) : 100,
            'page' => SitePage::where('key', 'event-tracker')->first(),
            'teams' => EventTeam::all(),
        ]);
    }

    /**
     * Show the clear event currency modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getClearEventCurrency()
    {
        return view('admin.events._clear_event_currency', [
            'currency' => Currency::find(Settings::get('event_currency'))
        ]);
    }

    /**
     * Zeros event points for all users.
     *
     * @param  \Illuminate\Http\Request        $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postClearEventCurrency(Request $request, EventService $service)
    {
        if($service->clearEventCurrency(Auth::user())) {
            flash('Event currency cleared successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Processes team info updates.
     *
     * @param  \Illuminate\Http\Request        $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEventTeams(Request $request, EventService $service)
    {
        $request->validate(EventTeam::$validationRules);
        $data = $request->only(['name', 'image', 'remove_image', 'score']);
        if($service->updateTeams($data, Auth::user())) {
            flash('Teams updated successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

}
