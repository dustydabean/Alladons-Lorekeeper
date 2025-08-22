<?php


namespace App\Http\Controllers;


use Auth;
use db;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SitePage;


class GuideController extends Controller
{
    public function getguide()     
    {
    return view('guide.guide');     
    }

}
