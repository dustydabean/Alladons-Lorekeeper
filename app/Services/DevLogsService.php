<?php

namespace App\Services;

use App\Models\DevLogs;
use App\Models\User\User;
use DB;

class DevLogsService extends Service {
    /*
    |--------------------------------------------------------------------------
    | Logs Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of dev logs.
    |
    */

    /**
     * Creates a dev log post.
     *
     * @param array $data
     * @param User  $user
     *
     * @return bool|DevLogs
     */
    public function createdevLogs($data, $user) {
        DB::beginTransaction();

        try {
            $data['parsed_text'] = parse($data['text']);
            $data['user_id'] = $user->id;
            if (!isset($data['is_visible'])) {
                $data['is_visible'] = 0;
            }

            $devLogs = DevLogs::create($data);

            if ($devLogs->is_visible) {
                $this->alertUsers();
            }

            return $this->commitReturn($devLogs);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Updates a dev log post.
     *
     * @param DevLogs $devLogs
     * @param array   $data
     * @param User    $user
     *
     * @return bool|DevLogs
     */
    public function updateDevLogs($devLogs, $data, $user) {
        DB::beginTransaction();

        try {
            $data['parsed_text'] = parse($data['text']);
            $data['user_id'] = $user->id;
            if (!isset($data['is_visible'])) {
                $data['is_visible'] = 0;
            }
            if (isset($data['bump']) && $data['is_visible'] == 1 && $data['bump'] == 1) {
                $this->alertUsers();
            }

            $devLogs->update($data);

            return $this->commitReturn($devLogs);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a dev log post.
     *
     * @param DevLogs $devLogs
     *
     * @return bool
     */
    public function deletedevLogs($devLogs) {
        DB::beginTransaction();

        try {
            $devLogs->delete();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Updates queued dev log posts to be visible and alert users when
     * they should be posted.
     *
     * @return bool
     */
    public function updateQueue() {
        $count = DevLogs::shouldBeVisible()->count();
        if ($count) {
            DB::beginTransaction();

            try {
                DevLogs::shouldBeVisible()->update(['is_visible' => 1]);
                $this->alertUsers();

                return $this->commitReturn(true);
            } catch (\Exception $e) {
                $this->setError('error', $e->getMessage());
            }

            return $this->rollbackReturn(false);
        }
    }

    /**
     * Updates the unread dev log flag for all users so that
     * the new dev log notification is displayed.
     *
     * @return bool
     */
    private function alertUsers() {
        User::query()->update(['is_dev_logs_unread' => 1]);

        return true;
    }
}
