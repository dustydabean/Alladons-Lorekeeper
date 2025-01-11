<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;

use App\Models\Feature\FeatureCategory;
use App\Models\Feature\Feature;
use App\Models\Species\Species;
use App\Models\Species\Subtype;
use App\Models\Feature\FeatureExample;

class FeatureService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Feature Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of feature categories and features.
    |
    */

    /**********************************************************************************************

        FEATURE CATEGORIES

    **********************************************************************************************/

    /**
     * Create a category.
     *
     * @param  array                 $data
     * @param  \App\Models\User\User $user
     * @return \App\Models\Feature\FeatureCategory|bool
     */
    public function createFeatureCategory($data, $user)
    {
        DB::beginTransaction();

        try {
            $data = $this->populateCategoryData($data);

            $image = null;
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }
            else $data['has_image'] = 0;

            $category = FeatureCategory::create($data);

            if ($image) $this->handleImage($image, $category->categoryImagePath, $category->categoryImageFileName);

            return $this->commitReturn($category);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Update a category.
     *
     * @param  \App\Models\Feature\FeatureCategory  $category
     * @param  array                                $data
     * @param  \App\Models\User\User                $user
     * @return \App\Models\Feature\FeatureCategory|bool
     */
    public function updateFeatureCategory($category, $data, $user)
    {
        DB::beginTransaction();

        try {
            // More specific validation
            if(FeatureCategory::where('name', $data['name'])->where('id', '!=', $category->id)->exists()) throw new \Exception("The name has already been taken.");

            $data = $this->populateCategoryData($data, $category);

            $image = null;
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }

            $category->update($data);

            if ($category) $this->handleImage($image, $category->categoryImagePath, $category->categoryImageFileName);

            return $this->commitReturn($category);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Handle category data.
     *
     * @param  array                                     $data
     * @param  \App\Models\Feature\FeatureCategory|null  $category
     * @return array
     */
    private function populateCategoryData($data, $category = null)
    {
        if(isset($data['description']) && $data['description']) $data['parsed_description'] = parse($data['description']);

        if(isset($data['remove_image']))
        {
            if($category && $category->has_image && $data['remove_image'])
            {
                $data['has_image'] = 0;
                $this->deleteImage($category->categoryImagePath, $category->categoryImageFileName);
            }
            unset($data['remove_image']);
        }

        return $data;
    }

    /**
     * Delete a category.
     *
     * @param  \App\Models\Feature\FeatureCategory  $category
     * @return bool
     */
    public function deleteFeatureCategory($category)
    {
        DB::beginTransaction();

        try {
            // Check first if the category is currently in use
            if(Feature::where('feature_category_id', $category->id)->exists()) throw new \Exception("A trait with this category exists. Please change its category first.");

            if($category->has_image) $this->deleteImage($category->categoryImagePath, $category->categoryImageFileName);
            $category->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Sorts category order.
     *
     * @param  array  $data
     * @return bool
     */
    public function sortFeatureCategory($data)
    {
        DB::beginTransaction();

        try {
            // explode the sort array and reverse it since the order is inverted
            $sort = array_reverse(explode(',', $data));

            foreach($sort as $key => $s) {
                FeatureCategory::where('id', $s)->update(['sort' => $key]);
            }

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }


    /**********************************************************************************************

        FEATURES

    **********************************************************************************************/

    /**
     * Creates a new feature.
     *
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Feature\Feature
     */
    public function createFeature($data, $user)
    {
        DB::beginTransaction();

        try {
            if(isset($data['feature_category_id']) && $data['feature_category_id'] == 'none') $data['feature_category_id'] = null;
            if(isset($data['species_id']) && $data['species_id'] == 'none') $data['species_id'] = null;
            if(isset($data['subtype_id']) && $data['subtype_id'] == 'none') $data['subtype_id'] = null;

            if((isset($data['feature_category_id']) && $data['feature_category_id']) && !FeatureCategory::where('id', $data['feature_category_id'])->exists()) throw new \Exception("The selected trait category is invalid.");
            if((isset($data['species_id']) && $data['species_id']) && !Species::where('id', $data['species_id'])->exists()) throw new \Exception("The selected species is invalid.");
            if(isset($data['subtype_id']) && $data['subtype_id'])
            {
                $subtype = Subtype::find($data['subtype_id']);
                if(!(isset($data['species_id']) && $data['species_id'])) throw new \Exception('Species must be selected to select a subtype.');
                if(!$subtype || $subtype->species_id != $data['species_id']) throw new \Exception('Selected subtype invalid or does not match species.');
            }

            $data = $this->populateData($data);

            $image = null;
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }
            else $data['has_image'] = 0;

            $feature = Feature::create($data);

            if ($image) $this->handleImage($image, $feature->imagePath, $feature->imageFileName);

            return $this->commitReturn($feature);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates a feature.
     *
     * @param  \App\Models\Feature\Feature  $feature
     * @param  array                        $data
     * @param  \App\Models\User\User        $user
     * @return bool|\App\Models\Feature\Feature
     */
    public function updateFeature($feature, $data, $user)
    {
        DB::beginTransaction();

        try {
            if(isset($data['feature_category_id']) && $data['feature_category_id'] == 'none') $data['feature_category_id'] = null;
            if(isset($data['species_id']) && $data['species_id'] == 'none') $data['species_id'] = null;
            if(isset($data['subtype_id']) && $data['subtype_id'] == 'none') $data['subtype_id'] = null;

            // More specific validation
            if(Feature::where('name', $data['name'])->where('id', '!=', $feature->id)->exists()) throw new \Exception("The name has already been taken.");
            if((isset($data['feature_category_id']) && $data['feature_category_id']) && !FeatureCategory::where('id', $data['feature_category_id'])->exists()) throw new \Exception("The selected trait category is invalid.");
            if((isset($data['species_id']) && $data['species_id']) && !Species::where('id', $data['species_id'])->exists()) throw new \Exception("The selected species is invalid.");
            if(isset($data['subtype_id']) && $data['subtype_id'])
            {
                $subtype = Subtype::find($data['subtype_id']);
                if(!(isset($data['species_id']) && $data['species_id'])) throw new \Exception('Species must be selected to select a subtype.');
                if(!$subtype || $subtype->species_id != $data['species_id']) throw new \Exception('Selected subtype invalid or does not match species.');
            }

            $data = $this->populateData($data);

            $image = null;
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }

            $feature->update($data);

            if ($feature) {
                $this->handleImage($image, $feature->imagePath, $feature->imageFileName);
            }

            return $this->commitReturn($feature);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Processes user input for creating/updating a feature.
     *
     * @param  array                        $data
     * @param  \App\Models\Feature\Feature  $feature
     * @return array
     */
    private function populateData($data, $feature = null)
    {
        if(isset($data['description']) && $data['description']) $data['parsed_description'] = parse($data['description']);
        if(isset($data['species_id']) && $data['species_id'] == 'none') $data['species_id'] = null;
        if(isset($data['feature_category_id']) && $data['feature_category_id'] == 'none') $data['feature_category_id'] = null;
        if(isset($data['remove_image']))
        {
            if($feature && $feature->has_image && $data['remove_image'])
            {
                $data['has_image'] = 0;
                $this->deleteImage($feature->imagePath, $feature->imageFileName);
            }
            unset($data['remove_image']);
        }

        return $data;
    }

    /**
     * Deletes a feature.
     *
     * @param  \App\Models\Feature\Feature  $feature
     * @return bool
     */
    public function deleteFeature($feature)
    {
        DB::beginTransaction();

        try {
            // Check first if the feature is currently in use
            if(DB::table('character_features')->where('feature_id', $feature->id)->exists()) throw new \Exception("A character with this trait exists. Please remove the trait first.");

            if($feature->has_image) $this->deleteImage($feature->imagePath, $feature->imageFileName);
            $feature->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**********************************************************************************************

    EXAMPLES

     **********************************************************************************************/

    /**
     * Creates a new example
     */
    public function createFeatureExample($feature, $data)
    {
        DB::beginTransaction();

        try {
            if (!$feature) {
                abort(404);
            }

            $image = null;
            if (isset($data['image']) && $data['image']) {
                $image = $data['image'];
                $data['hash'] = randomString(10);
                unset($data['image']);
            }

            $data['feature_id'] = $feature->id;

            $example = FeatureExample::create($data);

            if ($image) {
                $this->handleImage($image, $example->imagePath, $example->imageFileName);
            } else {
                throw new \Exception("Please upload an image to the example.");
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Edits the variants on a border.
     *
     * @param mixed $variant
     * @param mixed $data
     * @param mixed $type
     */
    public function editFeatureExample($example, $data)
    {
        DB::beginTransaction();

        try {
            if (!$example) {
                abort(404);
            }

            $image = null;
            if (isset($data['image']) && $data['image']) {
                $image = $data['image'];
                $data['hash'] = randomString(10);
            }

            $example->update($data);

            if (isset($data['image'])) {
                if ($image) {
                    $this->handleImage($image, $example->imagePath, $example->imageFileName);
                }

                unset($data['image']);
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Deletes an example image.
     *
     * @param  \App\Models\Feature\Feature  $feature
     * @return bool
     */
    public function deleteFeatureExample($example)
    {
        DB::beginTransaction();

        try {
            $this->deleteImage($example->imagePath, $example->imageFileName);
            $example->delete();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Sort a trait's examples
     */
    public function sortFeatureExamples($data, $feature)
    {
        DB::beginTransaction();

        try {
            // explode the sort array and reverse it since the order is inverted
            $sort = array_reverse(explode(',', $data));

            foreach ($sort as $key => $s) {
                FeatureExample::where('feature_id', $feature->id)->where('id', $s)->update(['sort' => $key]);
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }
}
