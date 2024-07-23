<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Models\Faq;
use Config;
use DB;

class FaqService extends Service {
    /*
    |--------------------------------------------------------------------------
    | Faq Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of faq categories and faq.
    |
    */

    /**********************************************************************************************

        FAQ

    **********************************************************************************************/

    /**
     * Creates a new faq.
     *
     * @param array                 $data
     * @param \App\Models\User\User $user
     *
     * @return \App\Models\Faq\Faq|bool
     */
    public function createFaq($data, $user) {
        DB::beginTransaction();

        try {
            if (!isset($data['is_visible'])) {
                $data['is_visible'] = 0;
            }

            $data['parsed_answer'] = parse($data['answer']);

            // make tags array json
            if (isset($data['tags'])) {
                $data['tags'] = json_encode($data['tags']);
            }

            $faq = Faq::create($data);

            if (!$this->logAdminAction($user, 'Created FAQ Question', 'Created FAQ Question:'. Str::words($faq->question, 5, '...'))) {
                throw new \Exception('Failed to log admin action.');
            }

            return $this->commitReturn($faq);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Updates an faq.
     *
     * @param \App\Models\Faq\Faq $faq
     * @param array                 $data
     * @param \App\Models\User\User $user
     *
     * @return \App\Models\Faq\Faq|bool
     */
    public function updateFaq($faq, $data, $user) {
        DB::beginTransaction();

        try {

            if (!isset($data['is_visible'])) {
                $data['is_visible'] = 0;
            }

            $data['parsed_answer'] = parse($data['answer']);

            // make tags array json
            if (isset($data['tags'])) {
                $data['tags'] = json_encode($data['tags']);
            }

            if (!$this->logAdminAction($user, 'Updated FAQ Question', 'Updated FAQ Question:'. Str::words($faq->question, 5, '...'))) {
                throw new \Exception('Failed to log admin action.');
            }

            $faq->update($data);

            return $this->commitReturn($faq);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Deletes an faq.
     *
     * @param \App\Models\Faq\Faq $faq
     * @param mixed                 $user
     *
     * @return bool
     */
    public function deleteFaq($faq, $user) {
        DB::beginTransaction();

        try {
            if (!$this->logAdminAction($user, 'Deleted FAQ Question', 'Deleted FAQ Question:'. Str::words($faq->question, 5, '...'))) {
                throw new \Exception('Failed to log admin action.');
            }
            $faq->delete();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }
}
