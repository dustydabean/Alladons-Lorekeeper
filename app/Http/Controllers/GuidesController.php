<?php


namespace App\Http\Controllers;


use Auth;
use db;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SitePage;


class GuidesController extends Controller
{
    public function getguides() 
    {
    return view('guides.guides', [ 
    'page' => SitePage::where('key', 'guides')->first()
        ]);
    }
}
