<?php

namespace App\Http\Controllers;

class RulesController extends Controller {
    public function getRules() {
        return view('rules.rules');
    }
}
