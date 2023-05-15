<?php

namespace App\Services;

use App\Models\Character\CharacterImage;
use App\Models\Character\CharacterTransformation as Transformation;
use DB;

class TransformationService extends Service {
    /*
    |--------------------------------------------------------------------------
    | Transformation Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of character transformation.
    |
    */

    /**
     * Creates a new transformation.
     *
     * @param array                 $data
     * @param \App\Models\User\User $user
     *
     * @return \App\Models\Transformation\Transformation|bool
     */
    public function createTransformation($data, $user) {
        DB::beginTransaction();

        try {
            $data = $this->populateTransformationData($data);

            $image = null;
            if (isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            } else {
                $data['has_image'] = 0;
            }

            $transformation = Transformation::create($data);

            if ($image) {
                $this->handleImage($image, $transformation->transformationImagePath, $transformation->transformationImageFileName);
            }

            return $this->commitReturn($transformation);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Updates a transformation.
     *
     * @param \App\Models\Transformation\Transformation $transformation
     * @param array                                     $data
     * @param \App\Models\User\User                     $user
     *
     * @return \App\Models\Transformation\Transformation|bool
     */
    public function updateTransformation($transformation, $data, $user) {
        DB::beginTransaction();

        try {
            $data = $this->populateTransformationData($data, $transformation);

            $image = null;
            if (isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }

            $transformation->update($data);

            if ($transformation) {
                $this->handleImage($image, $transformation->transformationImagePath, $transformation->transformationImageFileName);
            }

            return $this->commitReturn($transformation);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a transformation.
     *
     * @param \App\Models\Transformation\Transformation $transformation
     *
     * @return bool
     */
    public function deleteTransformation($transformation) {
        DB::beginTransaction();

        try {
            // Check first if characters with this transformation exists
            if (CharacterImage::where('transformation_id', $transformation->id)->exists()) {
                throw new \Exception('A character image with this transformation exists. Please change or remove its transformation first.');
            }

            if ($transformation->has_image) {
                $this->deleteImage($transformation->transformationImagePath, $transformation->transformationImageFileName);
            }
            $transformation->delete();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Sorts transformation order.
     *
     * @param array $data
     *
     * @return bool
     */
    public function sortTransformations($data) {
        DB::beginTransaction();

        try {
            // explode the sort array and reverse it since the order is inverted
            $sort = array_reverse(explode(',', $data));

            foreach ($sort as $key => $s) {
                Transformation::where('id', $s)->update(['sort' => $key]);
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Processes user input for creating/updating a transformation.
     *
     * @param array                                     $data
     * @param \App\Models\Transformation\Transformation $transformation
     *
     * @return array
     */
    private function populateTransformationData($data, $transformation = null) {
        if (isset($data['description']) && $data['description']) {
            $data['parsed_description'] = parse($data['description']);
        }

        if (isset($data['remove_image'])) {
            if ($transformation && $transformation->has_image && $data['remove_image']) {
                $data['has_image'] = 0;
                $this->deleteImage($transformation->transformationImagePath, $transformation->transformationImageFileName);
            }
            unset($data['remove_image']);
        }

        return $data;
    }
}
