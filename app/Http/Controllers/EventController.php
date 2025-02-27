<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Facades\Settings;

use App\Models\Currency\Currency;
use App\Models\EventTeam;
use App\Models\User\UserCurrency;
use App\Models\SitePage;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Event Controller
    |--------------------------------------------------------------------------
    |
    | Displays information about events based on the current settings,
    | as well as allowing users to join a team if enabled/configured.
    |
    */

    /**
     * Shows the event currency tracking page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEventTracking(Request $request)
    {
        if(!Settings::get('event_global_score') && !Settings::get('event_teams')) {
            abort (404);
        }

        if(Settings::get('event_global_score')) {
            $total = UserCurrency::where('user_id', Settings::get('admin_user'))->where('currency_id', Settings::get('event_currency'))->first();
        }

        return view('events.event_tracking', [
            'currency' => Currency::find(Settings::get('event_currency')),
            'total' => isset($total) ? $total : null,
            'progress' => isset($total) && $total ? ($total->quantity < Settings::get('event_global_goal') ? ($total->quantity/Settings::get('event_global_goal'))*100 : 100) : 0,
            'inverseProgress' => isset($total) && $total ? ($total->quantity < Settings::get('event_global_goal') ? 100-(($total->quantity/Settings::get('event_global_goal'))*100) : 0) : 100,
            'teams' => EventTeam::all(),
            'page' => SitePage::where('key', 'event-tracker')->first(),
        ]);
    }

    /**
     * Joins a team.
     *
     * @param  \Illuminate\Http\Request        $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postJoinTeam(Request $request, $id)
    {
        $team = EventTeam::where('id', $id)->first();
        if(!$team) {
            abort (404);
        }

        if(Auth::user()->settings->team_id != null) {
            flash ('You are already part of a team!')->error();
            return redirect()->back();
        }

        if(Auth::user()->settings->update(['team_id' => $team->id])) {
            flash('Team joined successfully!')->success();
        }
        else {
            flash('Failed to join team!')->error();
        }
        return redirect()->back();
    }
}
