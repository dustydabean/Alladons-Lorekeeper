<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DevLogs;
use App\Services\DevLogsService;
use Auth;
use Illuminate\Http\Request;

class DevLogsController extends Controller {
    /**
     * Shows the logs index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex() {
        return view('admin.devlogs.devlogs', [
            'devLogses' => DevLogs::orderBy('updated_at', 'DESC')->paginate(20),
        ]);
    }

    /**
     * Shows the create dev log page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreatedevLogs() {
        return view('admin.devlogs.create_edit_devlogs', [
            'devLogs' => new DevLogs,
        ]);
    }

    /**
     * Shows the edit dev log page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditdevLogs($id) {
        $devLogs = DevLogs::find($id);
        if (!$devLogs) {
            abort(404);
        }

        return view('admin.devlogs.create_edit_devlogs', [
            'devLogs' => $devLogs,
        ]);
    }

    /**
     * Creates or edits a dev log page.
     *
     * @param App\Services\DevLogsService $service
     * @param int|null                    $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditdevLogs(Request $request, DevLogsService $service, $id = null) {
        $id ? $request->validate(DevLogs::$updateRules) : $request->validate(DevLogs::$createRules);
        $data = $request->only([
            'title', 'text', 'post_at', 'is_visible', 'bump',
        ]);
        if ($id && $service->updateDevLogs(DevLogs::find($id), $data, Auth::user())) {
            flash('Dev log updated successfully.')->success();
        } elseif (!$id && $devLogs = $service->createdevLogs($data, Auth::user())) {
            flash('Dev log created successfully.')->success();

            return redirect()->to('admin/devlogs/edit/'.$devLogs->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the dev log deletion modal.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeletedevLogs($id) {
        $devLogs = DevLogs::find($id);

        return view('admin.devlogs._delete_devlogs', [
            'devLogs' => $devLogs,
        ]);
    }

    /**
     * Deletes a dev logs page.
     *
     * @param App\Services\DevLogsService $service
     * @param int                         $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeletedevLogs(Request $request, DevLogsService $service, $id) {
        if ($id && $service->deletedevLogs(DevLogs::find($id))) {
            flash('Dev log deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/devlogs');
    }
}
