<?php

namespace App\Http\Controllers;

use Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Activity;

class ActivityController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Activity Controller
    |--------------------------------------------------------------------------
    |
    | Handles viewing the shop index, shops and purchasing from shops.
    |
    */

    /**
     * Shows the shop index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex() {
        return view('activities.index', [
            'activities' => Activity::where('is_active', 1)->orderBy('sort', 'DESC')->get()
        ]);
    }

    /**
     * Shows an Activity.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getActivity($id) {
        $activity = Activity::where('id', $id)->where('is_active', 1)->first();
        if (!$activity) abort(404);
        return view('activities.activity', [
            'activity' => $activity,
        ] + $activity->service->getActData($activity));
    }



    /**
     * Acts on the Activities module
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\ShopManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postAct(Request $request, $id) {
        $activity = Activity::where('id', $id)->where('is_active', 1)->first();
        $service = $activity->service;
        if (!$activity) abort(404);
        if ($service->act($activity, $request->all(), Auth::user())) {
            // Do nothing because the service will call flash directly
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
}
