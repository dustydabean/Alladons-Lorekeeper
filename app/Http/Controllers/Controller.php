<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use View;
use App\Models\Theme;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Creates a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->defaultTheme = Theme::where('is_default',true)->first();
        View::share('defaultTheme', $this->defaultTheme);
    }
}
