<?php

namespace App\Http\Controllers;

use Auth;
use \Datetime;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Services\DailyService;

use App\Models\Daily\Daily;
use App\Models\Daily\DailyTimer;
use App\Models\Item\Item;
use App\Models\Item\ItemTag;
use App\Models\Currency\Currency;
use App\Models\Item\ItemCategory;
use App\Models\User\UserItem;

class DailyController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Daily Controller
    |--------------------------------------------------------------------------
    |
    | Handles viewing the Daily index, dailies and doing dailies.
    |
    */

    /**
     * Shows the Daily index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('dailies.index', [
            'dailies' => Daily::where('is_active', 1)->orderBy('sort', 'DESC')->get()
        ]);
    }

    /**
     * Shows a Daily.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDaily($id, DailyService $service)
    {
        $daily = Daily::where('id', $id)->where('is_active', 1)->first();
        $timer = (Auth::user()) ? DailyTimer::where('daily_id', $daily->id)->where("user_id", Auth::user()->id)->first() : null;
        if(!$daily) abort(404);

        
        
        return view('dailies.dailies', [
            'daily' => $daily,
            'dailies' => Daily::where('is_active', 1)->orderBy('sort', 'DESC')->get(),
            'timer' => $timer,
            'cooldown' => $service->getDailyCooldown($daily, $timer)
        ]);
    }



    /**
     * Handles a daily roll.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\DailyService  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postRoll(Request $request, DailyService $service)
    {
        $daily = Daily::where('id', $request->daily_id)->where('is_active', 1)->first();
        $request->validate(DailyTimer::$createRules);
        $rewards = $service->rollDaily($request->only(['daily_id']), Auth::user());

        if(!$rewards) {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        } else {
            $rolledRewards = 0;
            foreach($rewards as $type => $rewardList){
                foreach($rewardList as $reward){
                    $rolledRewards += 1;
                    flash('You received '.$reward['quantity'].'x '.$reward['asset']->name."!");
                }
            }
            if($rolledRewards <= 0) flash('You received nothing. Better luck next time!');
        }
        return redirect()->back();

    }

}


