<?php

namespace App\Http\Controllers\Admin\Data;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Services\ActivityService;
use Illuminate\Http\Request;

class ActivityController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Admin / Activity Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of activities and activity stock.
    |
    */

    /**
     * Shows the activity index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex() {
        return view('admin.activities.activities', [
            'activities' => Activity::orderBy('sort', 'DESC')->get(),
        ]);
    }

    /**
     * Shows the create activity page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateActivity() {
        return view('admin.activities.create_edit_activity', [
            'activity' => new Activity,
            'modules'  => config('lorekeeper.activity_modules'),
        ]);
    }

    /**
     * Shows the edit activity page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditActivity($id) {
        $activity = Activity::find($id);
        if (!$activity) {
            abort(404);
        }

        return view('admin.activities.create_edit_activity', [
            'activity' => $activity,
            'modules'  => config('lorekeeper.activity_modules'),
        ] + $activity->service->getEditData());
    }

    /**
     * Creates or edits an Activity.
     *
     * @param App\Services\ActivityService $service
     * @param int|null                     $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditActivity(Request $request, ActivityService $service, $id = null) {
        $id ? $request->validate(Activity::$updateRules) : $request->validate(Activity::$createRules);
        $data = $request->only([
            'name', 'description', 'image', 'remove_image', 'is_active', 'module',
        ]);
        if ($id && $service->updateActivity(Activity::find($id), $data)) {
            flash('Activity updated successfully.')->success();
        } elseif (!$id && $activity = $service->createActivity($data)) {
            flash('Activity created successfully.')->success();

            return redirect()->to('admin/data/activities/edit/'.$activity->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Edits an Activity's module.
     *
     * @param App\Services\ActivityService $service
     * @param int                          $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEditModule(Request $request, ActivityService $service, $id) {
        $data = $request->all();
        if ($service->updateModule(Activity::find($id), $data)) {
            flash('Activity module settings updated successfully.')->success();

            return redirect()->back();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the activity deletion modal.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteActivity($id) {
        $activity = Activity::find($id);

        return view('admin.activities._delete_activity', [
            'activity' => $activity,
        ]);
    }

    /**
     * Deletes an Activity.
     *
     * @param App\Services\ActivityService $service
     * @param int                          $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteActivity(Request $request, ActivityService $service, $id) {
        if ($id && $service->deleteActivity(Activity::find($id))) {
            flash('Activity deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/data/activities');
    }

    /**
     * Sorts activities.
     *
     * @param App\Services\ActivityService $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSortActivity(Request $request, ActivityService $service) {
        if ($service->sortActivities($request->get('sort'))) {
            flash('Activity order updated successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }
}
