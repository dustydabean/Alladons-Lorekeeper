<?php

namespace App\Http\Controllers;

class GuideController extends Controller {
    public function getguide() {
        return view('guide.guide');
    }
}
