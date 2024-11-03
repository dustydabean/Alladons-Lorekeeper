<?php

namespace App\Services;

use App\Models\Activity;
use DB;

class ActivityService extends Service {
    /*
      |--------------------------------------------------------------------------
      | Activity Service
      |--------------------------------------------------------------------------
      |
      | Handles the creation and editing of Activities
      |
      */

    /**********************************************************************************************

          SHOPS

     **********************************************************************************************/

    /**
     * Creates a new shop.
     *
     * @param array $data
     *
     * @return \App\Models\Shop\Shop|bool
     */
    public function createActivity($data) {
        DB::beginTransaction();

        try {
            $data = $this->populateActivityData($data);

            $image = null;
            if (isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            } else {
                $data['has_image'] = 0;
            }

            $activity = Activity::create($data);

            if ($image) {
                $this->handleImage($image, $activity->ImagePath, $activity->ImageFileName);
            }

            return $this->commitReturn($activity);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Updates a shop.
     *
     * @param array $data
     * @param mixed $activity
     *
     * @return \App\Models\Shop\Shop|bool
     */
    public function updateActivity($activity, $data) {
        DB::beginTransaction();

        try {
            // More specific validation
            if (Activity::where('name', $data['name'])->where('id', '!=', $activity->id)->exists()) {
                throw new \Exception('The name has already been taken.');
            }

            $data = $this->populateActivityData($data, $activity);

            $image = null;
            if (isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }

            // If changing modules, clear out the old data
            if ($activity->module !== $data['module']) {
                $activity->data = null;
                $activity->save();
            }

            $activity->update($data);

            if ($activity) {
                $this->handleImage($image, $activity->ImagePath, $activity->ImageFileName);
            }

            return $this->commitReturn($activity);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a shop.
     *
     * @param mixed $activity
     *
     * @return bool
     */
    public function deleteActivity($activity) {
        DB::beginTransaction();

        try {
            if ($activity->has_image) {
                $this->deleteImage($activity->ImagePath, $activity->ImageFileName);
            }
            $activity->delete();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Sorts shop order.
     *
     * @param string $data
     *
     * @return bool
     */
    public function sortActivity($data) {
        DB::beginTransaction();

        try {
            // explode the sort array and reverse it since the order is inverted
            $sort = array_reverse(explode(',', $data));

            foreach ($sort as $key => $s) {
                Activity::where('id', $s)->update(['sort' => $key]);
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    public function updateModule($activity, $data) {
        DB::beginTransaction();

        try {
            $activity->data = $activity->service->updateData($activity, $data);
            $activity->save();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Processes user input for creating/updating a shop.
     *
     * @param array      $data
     * @param mixed|null $activity
     *
     * @return array
     */
    private function populateActivityData($data, $activity = null) {
        if (isset($data['description']) && $data['description']) {
            $data['parsed_description'] = parse($data['description']);
        }
        $data['is_active'] = isset($data['is_active']);

        if (isset($data['remove_image'])) {
            if ($activity && $activity->has_image && $data['remove_image']) {
                $data['has_image'] = 0;
                $this->deleteImage($activity->ImagePath, $activity->ImageFileName);
            }
            unset($data['remove_image']);
        }

        return $data;
    }
}
