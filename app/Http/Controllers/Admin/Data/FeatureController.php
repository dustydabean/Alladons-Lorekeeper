<?php

namespace App\Http\Controllers\Admin\Data;

use App\Http\Controllers\Controller;
use App\Models\Feature\Feature;
use App\Models\Feature\FeatureCategory;
use App\Models\Feature\FeatureExample;
use App\Models\Rarity;
use App\Models\Species\Species;
use App\Models\Species\Subtype;
use App\Services\FeatureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeatureController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Admin / Feature Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of character feature categories and features
    | (AKA traits, which is a reserved keyword in PHP and thus can't be used).
    |
    */

    /**********************************************************************************************

        FEATURE CATEGORIES

    **********************************************************************************************/

    /**
     * Shows the feature category index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex() {
        return view('admin.features.feature_categories', [
            'categories' => FeatureCategory::orderBy('sort', 'DESC')->get(),
        ]);
    }

    /**
     * Shows the create feature category page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateFeatureCategory() {
        return view('admin.features.create_edit_feature_category', [
            'category' => new FeatureCategory,
        ]);
    }

    /**
     * Shows the edit feature category page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditFeatureCategory($id) {
        $category = FeatureCategory::find($id);
        if (!$category) {
            abort(404);
        }

        return view('admin.features.create_edit_feature_category', [
            'category' => $category,
        ]);
    }

    /**
     * Creates or edits a feature category.
     *
     * @param App\Services\FeatureService $service
     * @param int|null                    $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditFeatureCategory(Request $request, FeatureService $service, $id = null) {
        $id ? $request->validate(FeatureCategory::$updateRules) : $request->validate(FeatureCategory::$createRules);
        $data = $request->only([
            'name', 'description', 'image', 'remove_image', 'is_visible',
            'min_inheritable', 'max_inheritable',
        ]);
        if ($id && $service->updateFeatureCategory(FeatureCategory::find($id), $data, Auth::user())) {
            flash('Category updated successfully.')->success();
        } elseif (!$id && $category = $service->createFeatureCategory($data, Auth::user())) {
            flash('Category created successfully.')->success();

            return redirect()->to('admin/data/trait-categories/edit/'.$category->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the feature category deletion modal.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteFeatureCategory($id) {
        $category = FeatureCategory::find($id);

        return view('admin.features._delete_feature_category', [
            'category' => $category,
        ]);
    }

    /**
     * Creates or edits a feature category.
     *
     * @param App\Services\FeatureService $service
     * @param int|null                    $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteFeatureCategory(Request $request, FeatureService $service, $id) {
        if ($id && $service->deleteFeatureCategory(FeatureCategory::find($id), Auth::user())) {
            flash('Category deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/data/trait-categories');
    }

    /**
     * Sorts feature categories.
     *
     * @param App\Services\FeatureService $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSortFeatureCategory(Request $request, FeatureService $service) {
        if ($service->sortFeatureCategory($request->get('sort'))) {
            flash('Category order updated successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**********************************************************************************************

        FEATURES

    **********************************************************************************************/

    /**
     * Shows the feature index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getFeatureIndex(Request $request) {
        $query = Feature::query();
        $data = $request->only(['rarity_id', 'feature_category_id', 'species_id', 'subtype_id', 'name', 'sort', 'visibility', 'mut_level', 'mut_type', 'is_locked']);
        if (isset($data['rarity_id']) && $data['rarity_id'] != 'none') {
            $query->where('rarity_id', $data['rarity_id']);
        }
        if (isset($data['feature_category_id']) && $data['feature_category_id'] != 'none') {
            if ($data['feature_category_id'] == 'withoutOption') {
                $query->whereNull('feature_category_id');
            } else {
                $query->where('feature_category_id', $data['feature_category_id']);
            }
        }
        if (isset($data['species_id']) && $data['species_id'] != 'none') {
            if ($data['species_id'] == 'withoutOption') {
                $query->whereNull('species_id');
            } else {
                $query->where('species_id', $data['species_id']);
            }
        }
        if (isset($data['subtype_id']) && $data['subtype_id'] != 'none') {
            if ($data['subtype_id'] == 'withoutOption') {
                $query->whereNull('subtype_id');
            } else {
                $query->where('subtype_id', $data['subtype_id']);
            }
        }
        if (isset($data['name'])) {
            $query->where('name', 'LIKE', '%'.$data['name'].'%');
        }
        if (isset($data['mut_level']) && $data['mut_level'] > '0') {
            $query->where('mut_level', $data['mut_level']);
        }
        if (isset($data['mut_type']) && $data['mut_type'] > '0') {
            $query->where('mut_type', $data['mut_type']);
        }
        if (isset($data['is_locked']) && $data['is_locked'] != 'none') {
            $query->locked($data['is_locked']);
        }

        if (isset($data['visibility']) && $data['visibility'] != 'none') {
            if ($data['visibility'] == 'visibleOnly') {
                $query->where('is_visible', '=', 1);
            } else {
                $query->where('is_visible', '=', 0);
            }
        }

        if (isset($data['sort'])) {
            switch ($data['sort']) {
                case 'alpha':
                    $query->sortAlphabetical();
                    break;
                case 'alpha-reverse':
                    $query->sortAlphabetical(true);
                    break;
                case 'category':
                    $query->sortCategory();
                    break;
                case 'rarity':
                    $query->sortRarity();
                    break;
                case 'rarity-reverse':
                    $query->sortRarity(true);
                    break;
                case 'species':
                    $query->sortSpecies();
                    break;
                case 'subtypes':
                    $query->sortSubtype();
                    break;
                case 'newest':
                    $query->sortNewest();
                    break;
                case 'oldest':
                    $query->sortOldest();
                    break;
            }
        } else {
            $query->sortOldest();
        }

        return view('admin.features.features', [
            'features'   => $query->paginate(20)->appends($request->query()),
            'rarities'   => ['none' => 'Any Rarity'] + Rarity::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'specieses'  => ['none' => 'Any Species'] + ['withoutOption' => 'Without Species'] + Species::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'subtypes'   => ['none' => 'Any Subtype'] + ['withoutOption' => 'Without Subtype'] + Subtype::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'categories' => ['none' => 'Any Category'] + ['withoutOption' => 'Without Category'] + FeatureCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'levels'     => ['0' => 'Any Level', '1' => 'Minor', '2' => 'Major'],
            'types'      => ['0' => 'Any Type', '1' => 'Breed Only', '2' => 'Custom Requestable'],
        ]);
    }

    /**
     * Shows the create feature page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateFeature() {
        return view('admin.features.create_edit_feature', [
            'feature'    => new Feature,
            'rarities'   => ['none' => 'Select a Rarity'] + Rarity::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'specieses'  => ['none' => 'No restriction'] + Species::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'subtypes'   => ['none' => 'No subtype'] + Subtype::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'categories' => ['none' => 'No category'] + FeatureCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Shows the edit feature page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditFeature($id) {
        $feature = Feature::find($id);
        if (!$feature) {
            abort(404);
        }

        return view('admin.features.create_edit_feature', [
            'feature'    => $feature,
            'rarities'   => ['none' => 'Select a Rarity'] + Rarity::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'specieses'  => ['none' => 'No restriction'] + Species::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'subtypes'   => ['none' => 'No subtype'] + Subtype::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'categories' => ['none' => 'No category'] + FeatureCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Creates or edits a feature.
     *
     * @param App\Services\FeatureService $service
     * @param int|null                    $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditFeature(Request $request, FeatureService $service, $id = null) {
        $id ? $request->validate(Feature::$updateRules) : $request->validate(Feature::$createRules);
        $data = $request->only([
            'name', 'species_id', 'subtype_id', 'rarity_id', 'feature_category_id', 'description', 'image', 'remove_image', 'is_visible', 'sex',
            'mut_level', 'mut_type', 'is_locked', 'code_id',
        ]);
        if ($id && $service->updateFeature(Feature::find($id), $data, Auth::user())) {
            flash('Trait updated successfully.')->success();
        } elseif (!$id && $feature = $service->createFeature($data, Auth::user())) {
            flash('Trait created successfully.')->success();

            return redirect()->to('admin/data/traits/edit/'.$feature->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the feature deletion modal.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteFeature($id) {
        $feature = Feature::find($id);

        return view('admin.features._delete_feature', [
            'feature' => $feature,
        ]);
    }

    /**
     * Deletes a feature.
     *
     * @param App\Services\FeatureService $service
     * @param int                         $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteFeature(Request $request, FeatureService $service, $id) {
        if ($id && $service->deleteFeature(Feature::find($id), Auth::user())) {
            flash('Trait deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/data/traits');
    }

    /**
     * Shows the edit subtype portion of the modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateEditFeatureSubtype(Request $request) {
        $species = $request->input('species');
        $subtype_id = $request->input('subtype_id');

        return view('admin.features._create_edit_feature_subtype', [
            'subtypes'   => ['0' => 'Select Subtype'] + Subtype::where('species_id', '=', $species)->orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'subtype_id' => $subtype_id,
        ]);
    }

    /**********************************************************************************************

            EXAMPLE IMAGES

        **********************************************************************************************/

    /**
     * get create/edit example.
     *
     * @param mixed $feature_id
     */
    public function getFeatureExamples($feature_id) {
        $feature = Feature::findOrFail($feature_id);

        return view('admin.features.feature_examples', [
            'feature'      => $feature,
            'examples'     => $feature->exampleImages,
        ]);
    }

    /**
     * get create/edit example.
     *
     * @param mixed      $feature_id
     * @param mixed|null $id
     */
    public function getCreateEditFeatureExample($feature_id, $id = null) {
        return view('admin.features._create_edit_example', [
            'feature'      => Feature::find($feature_id),
            'example'      => $id ? FeatureExample::find($id) : new FeatureExample,
        ]);
    }

    /**
     * post create/edit example.
     *
     * @param mixed      $feature_id
     * @param mixed|null $id
     */
    public function postCreateEditExample(Request $request, FeatureService $service, $feature_id, $id = null) {
        $data = $request->only(['summary', 'image']);
        if ($id && $service->editFeatureExample(FeatureExample::findOrFail($id), $data)) {
            flash('Example image edited successfully.')->success();
        } elseif (!$id && $service->createFeatureExample(Feature::find($feature_id), $data)) {
            flash('Example image created successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the feature deletion modal.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteFeatureExample($id) {
        $example = FeatureExample::find($id);

        return view('admin.features._delete_example', [
            'example' => $example,
            'feature' => $example->feature,
        ]);
    }

    /**
     * Deletes a feature.
     *
     * @param App\Services\FeatureService $service
     * @param int                         $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteFeatureExample(Request $request, FeatureService $service, $id) {
        if ($id && $service->deleteFeatureExample(FeatureExample::find($id))) {
            flash('Example deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Sort a trait's examples.
     *
     * @param mixed $feature_id
     */
    public function postSortFeatureExamples(Request $request, FeatureService $service, $feature_id) {
        if ($service->sortFeatureExamples($request->get('sort'), Feature::find($feature_id))) {
            flash('Example order updated successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }
}
