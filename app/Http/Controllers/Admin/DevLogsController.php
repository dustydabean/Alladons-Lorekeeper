<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Auth;

use App\Models\DevLogs;
use App\Services\DevLogsService;

use App\Http\Controllers\Controller;

class DevLogsController extends Controller
{
    /**
     * Shows the logs index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('admin.logs.logs', [
            'devLogses' => DevLogs::orderBy('updated_at', 'DESC')->paginate(20)
        ]);
    }
    
    /**
     * Shows the create dev log page. 
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateLogs()
    {
        return view('admin.logs.create_edit_logs', [
            'devLogs' => new DevLogs
        ]);
    }
    
    /**
     * Shows the edit dev log page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditLogs($id)
    {
        $devLogs = DevLogs::find($id);
        if(!$devLogs) abort(404);
        return view('admin.logs.create_edit_logs', [
            'devLogs' => $devLogs
        ]);
    }

    /**
     * Creates or edits a dev log page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\DevLogsService  $service
     * @param  int|null                  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditLogs(Request $request, DevLogsService $service, $id = null)
    {
        $id ? $request->validate(DevLogs::$updateRules) : $request->validate(DevLogs::$createRules);
        $data = $request->only([
            'title', 'text', 'post_at', 'is_visible', 'bump'
        ]);
        if($id && $service->updateDevLogs(DevLogs::find($id), $data, Auth::user())) {
            flash('Dev log updated successfully.')->success();
        }
        else if (!$id && $devLogs = $service->createLogs($data, Auth::user())) {
            flash('Dev log created successfully.')->success();
            return redirect()->to('admin/logs/edit/'.$devLogs->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    /**
     * Gets the dev log deletion modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteLogs($id)
    {
        $devLogs = DevLogs::find($id);
        return view('admin.logs._delete_logs', [
            'devLogs' => $devLogs,
        ]);
    }

    /**
     * Deletes a dev logs page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\DevLogsService  $service
     * @param  int                       $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteLogs(Request $request, DevLogsService $service, $id)
    {
        if($id && $service->deleteLogs(DevLogs::find($id))) {
            flash('Dev log deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/logs');
    }

}