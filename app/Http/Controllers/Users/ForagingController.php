<?php

namespace App\Http\Controllers\Users;

use Auth;
use DB;
use Config;
use Carbon\Carbon;

use Illuminate\Http\Request;

use App\Models\Character\Character;
use App\Models\User\UserForaging;
use App\Models\Foraging\Forage;

use App\Services\ForageService;
use App\Http\Controllers\Controller;

class ForagingController extends Controller
{
    /**
     * gets index
     */
    public function getIndex()
    {
        $userForage = DB::table('user_foraging')->where('user_id', Auth::user()->id )->first();

        if(!$userForage) {
            $userForage = UserForaging::create([
                'user_id' => Auth::user()->id,
            ]);
        }

        $characters = Auth::user()->characters()->pluck('slug', 'id');
        if (!count($characters) && config('lorekeeper.foraging.use_characters')) {
            if (config('lorekeeper.foraging.npcs.enabled')) {
                // check if we're using ids or category/rarity
                if (config('lorekeeper.foraging.npcs.use_ids')) {
                    $characters = Character::whereIn('id', config('lorekeeper.foraging.npcs.ids'))->pluck('slug', 'id');
                } else {
                    $characters = Character::where(config('lorekeeper.foraging.npcs.category_or_rarity'), config('lorekeeper.foraging.npcs.code'))->pluck('slug', 'id');
                }
                // if after all that there's still no characters
                if (!count($characters)) {
                    flash('You must have at least one character to forage.')->error();

                    return redirect()->back();
                }
            } else {
                flash('You must have at least one character to forage.')->error();

                return redirect()->back();
            }
        }

        return view('foraging.index', [
            'user' => Auth::user(),
            'tables' => Forage::visible(Auth::check() && Auth::user()->isStaff)->orderBy('name')->get(),
            'characters' => $characters
        ]);
    }

    /**
     * adds data to userforage table to start the timer
     */
    public function postForage($id, ForageService $service)
    {
        if($service->initForage($id, Auth::user()))
        {
            flash('You have begun to forage!')->info();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }

        return redirect()->back();

    }

    /**
     * when the time is up and the user can claim
     */
    public function postClaim(ForageService $service)
    {
        if($service->claimReward(Auth::user()))
        {
            flash('Forage successful!')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }

        return redirect()->back();
    }

    /**
     * edits the selected character for foraging
     *
     */
    public function postEditCharacter(Request $request, ForageService $service) {
        $id = $request->input('character_id');
        if ($service->editSelectedCharacter(Auth::user(), $id)) {
            flash('Character selected successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('foraging');
    }
}
