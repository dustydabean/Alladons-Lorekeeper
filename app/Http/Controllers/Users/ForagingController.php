<?php

namespace App\Http\Controllers\Users;

use Auth;
use File;
use Image;
use DB;

use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Notification;

use App\Models\User\User;
use App\Models\User\UserForaging;
use App\Models\Foraging\Forage;

use App\Services\ForageService;
use App\Services\UserService;

use App\Http\Controllers\Controller;

class ForagingController extends Controller
{

    public function getIndex()
    {
        $userForage = DB::table('user_foraging')->where('user_id', Auth::user()->id )->first();

            if(!$userForage) {
                $userForage = UserForaging::create([
                    'user_id' => Auth::user()->id,
                ]);
            }
        
        return view('foraging.index', [
            'user' => Auth::user(),
            'tables' => Forage::where('is_active', 1)->orderBy('name')->get(),
        ]);
    }

    public function postForage($id, Request $request)
    {
        $userForage = DB::table('user_foraging')->where('user_id', Auth::user()->id )->first();

            if(!$userForage) {
                $userForage = UserForaging::create([
                    'user_id' => Auth::user()->id,
                ]);
            }

        if($userForage->foraged == 1) throw new \Exception('You have already foraged today! Come back tomorrow.');

        $userForage->last_forage_id = $id;
        $userForage->last_foraged_at = carbon::now();
        $userForage->distribute_at = carbon::now()->addMinutes(60);
        $userForage->save();

        return redirect()->back();

    }
}