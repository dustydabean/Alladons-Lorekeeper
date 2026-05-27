<?php

namespace App\Http\Controllers;

use App\Models\SitePage;

class GuidesController extends Controller {
    public function getguides() {
        return view('guides.guides', [
            'page' => SitePage::where('key', 'guides')->first(),
        ]);
    }
}
