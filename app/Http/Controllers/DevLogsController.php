<?php

namespace App\Http\Controllers;

use Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View; 
use App\Http\Controllers\Controller;

use App\Models\DevLogs;
use App\Models\News;

class DevLogsController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Logs Controller
    |--------------------------------------------------------------------------
    |
    | Displays dev log posts and updates the user's dev-log read status.
    |
    */

    /**
     * Create a new controller instance.
     */
    public function __construct() {
        View::share('recentdevLogs', DevLogs::visible()->orderBy('updated_at', 'DESC')->take(10)->get());
        View::share('recentnews', News::visible()->orderBy('updated_at', 'DESC')->take(10)->get());
    }

    /**
     * Shows the logs index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        if(Auth::check() && Auth::user()->is_dev_logs_unread) Auth::user()->update(['is_dev_logs_unread' => 0]);
        return view('devlogs.index', [
            'devLogses' => DevLogs::visible()->orderBy('updated_at', 'DESC')->paginate(10),
        ]);
    }
    
    /**
     * Shows a dev log.
     *
     * @param  int          $id
     * @param  string|null  $slug
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getdevLogs($id, $slug = null)
    {
        $devLogs = DevLogs::where('id', $id)->where('is_visible', 1)->first();
        if(!$devLogs) abort(404);

        return view('devlogs.devlogs', [
            'devLogs' => $devLogs,
        ]);
    }
}
