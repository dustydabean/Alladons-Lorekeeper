<?php

namespace App\Http\Controllers;

use Auth;

use Illuminate\Http\Request;
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
     * Shows the logs index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        if(Auth::check() && Auth::user()->is_dev_logs_unread) Auth::user()->update(['is_dev_logs_unread' => 0]);
        return view('logs.index', [
            'newses' => News::visible()->orderBy('updated_at', 'DESC')->paginate(10),
            'devLogses' => DevLogs::visible()->orderBy('updated_at', 'DESC')->paginate(10)
        ]);
    }
    
    /**
     * Shows a dev log.
     *
     * @param  int          $id
     * @param  string|null  $slug
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getLogs($id, $slug = null)
    {
        $devLogs = DevLogs::where('id', $id)->where('is_visible', 1)->first();
        if(!$devLogs) abort(404);
        return view('logs.logs', ['devLogs' => $devLogs]);
    }
}
