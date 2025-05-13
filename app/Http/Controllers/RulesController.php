<?php

namespace App\Http\Controllers;

use Auth;
use db;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SitePage;


class RulesController extends Controller
{
    public function getRules()     
    {
    return view('rules.Rules');     
    }


    public function getRulesPage() 
    {
    return view('rules.RulesPage', [ 
    'page' => SitePage::where('key', 'RulesPage')->first()
        ]);
    }
}