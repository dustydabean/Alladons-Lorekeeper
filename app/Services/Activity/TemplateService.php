<?php

namespace App\Services\Activity;

use App\Services\Service;
use DB;

class TemplateService extends Service {
    /**
     * Retrieves any data that should be used in the activity module editing form on the admin side.
     *
     * @return array
     */
    public function getEditData() {
        return [];
    }

    /**
     * Retrieves any data that should be used in the activity module on the user side.
     *
     * @param mixed $activity
     *
     * @return array
     */
    public function getActData($activity) {
        return [];
    }

    /**
     * Processes the data attribute of the activity and returns it in the preferred format.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function getData($data) {
        return $data;
    }

    /**
     * Processes the data attribute of the activity and returns it in the preferred format.
     *
     * @param object $activity
     * @param array  $data
     *
     * @return bool
     */
    public function updateData($activity, $data) {
        return json_encode([]);
    }

    /**
     * Code executed by the act post url being hit
     * (expected from the user-side blade file at resources/views/activities/modules/name.blade.php).
     *
     * @param \App\Models\User\User $user
     * @param array                 $data
     * @param mixed                 $activity
     *
     * @return bool
     */
    public function act($activity, $data, $user) {
        DB::beginTransaction();

        try {
            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }
}
