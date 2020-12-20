<?php

namespace App\Http\Controllers\Users;

use Auth;
use File;
use Image;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Notification;

use App\Models\User\User;
use App\Models\Foraging\Forage;

use App\Services\ForageService;
use App\Services\UserService;

use App\Http\Controllers\Controller;

class ForagingController extends Controller
{

    public function getIndex()
    {
        return view('foraging.index', [
            'user' => Auth::user(),
            'tables' => Forage::where('is_active', 1)->orderBy('name')->get(),
        ]);
    }

    public function postForage($id, Request $request)
    {
        dd();
    }
}