<?php

namespace App\Http\Controllers\Admin\Data;

use App\Http\Controllers\Controller;
use App\Models\Character\CharacterTransformation as Transformation;
use App\Models\Species\Species;
use App\Services\TransformationService;
use Auth;
use Illuminate\Http\Request;

class TransformationController extends Controller {
    /**
     * Shows the transformation index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getTransformationIndex() {
        return view('admin.specieses.transformations', [
            'transformations' => Transformation::orderBy('sort', 'DESC')->get(),
        ]);
    }

    /**
     * Shows the create transformation page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateTransformation() {
        return view('admin.specieses.create_edit_transformation', [
            'transformation' => new Transformation,
            'specieses'      => Species::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Shows the edit transformation page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditTransformation($id) {
        $transformation = Transformation::find($id);
        if (!$transformation) {
            abort(404);
        }

        return view('admin.specieses.create_edit_transformation', [
            'transformation' => $transformation,
            'specieses'      => Species::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Creates or edits a transformation.
     *
     * @param App\Services\TransformationService $service
     * @param int|null                           $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditTransformation(Request $request, TransformationService $service, $id = null) {
        $id ? $request->validate(Transformation::$updateRules) : $request->validate(Transformation::$createRules);
        $data = $request->only([
            'species_id', 'name', 'description', 'image', 'remove_image',
        ]);
        if ($id && $service->updateTransformation(Transformation::find($id), $data, Auth::user())) {
            flash('Transformation updated successfully.')->success();
        } elseif (!$id && $transformation = $service->createTransformation($data, Auth::user())) {
            flash('Transformation created successfully.')->success();

            return redirect()->to('admin/data/transformations/edit/'.$transformation->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the transformation deletion modal.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteTransformation($id) {
        $transformation = Transformation::find($id);

        return view('admin.specieses._delete_transformation', [
            'transformation' => $transformation,
        ]);
    }

    /**
     * Deletes a transformation.
     *
     * @param App\Services\TransformationService $service
     * @param int                                $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteTransformation(Request $request, TransformationService $service, $id) {
        if ($id && $service->deleteTransformation(Transformation::find($id))) {
            flash('Transformation deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/data/transformations');
    }

    /**
     * Sorts transformations.
     *
     * @param App\Services\TransformationService $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSortTransformations(Request $request, TransformationService $service) {
        if ($service->sortTransformations($request->get('sort'))) {
            flash('Transformation order updated successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }
}
