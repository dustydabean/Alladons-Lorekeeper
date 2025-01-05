<?php

namespace App\Services;

use App\Models\Item\Item;
use App\Models\User\User;
use Carbon\Carbon;
use DB;

class HuntManager extends Service {
    /*
    |--------------------------------------------------------------------------
    | Scavenger Hunt Manager
    |--------------------------------------------------------------------------
    |
    | Handles user claiming of scavenger hunt targets.
    |
    */

    /**
     * Claims a scavenger hunt target.
     *
     * @param User  $user
     * @param mixed $target
     *
     * @return App\Models\ScavengerHunt\HuntTarget|bool
     */
    public function claimTarget($target, $user) {
        DB::beginTransaction();

        try {
            if (!$target) {
                throw new \Exception('Invalid target.');
            }
            // Check that the target's parent hunt exists and is active.
            $hunt = $target->hunt;
            if (!$hunt) {
                throw new \Exception('Invalid hunt.');
            }
            if (!$hunt->isActive) {
                throw new \Exception("This target\'s hunt isn\'t active.");
            }

            // Log that the user found this particular target
            $participantLog = $hunt->participants()->where('user_id', $user->id)->first();
            if ($participantLog && isset($participantLog[$target->targetField])) {
                throw new \Exception('You have already claimed this target.');
            }

            if (!$participantLog) {
                $participantLog = $hunt->participants()->create([
                    'user_id' => $user->id,
                ]);
            }
            $participantLog[$target->targetField] = Carbon::now();
            $participantLog->save();

            // Give the user the item(s)
            if (!(new InventoryManager)->creditItem(null, $user, 'Prize', [
                'data'  => $participantLog->itemData,
                'notes' => 'Claimed '.format_date($participantLog[$target->targetField]),
            ], $target->item, $target->quantity)) {
                throw new \Exception('Failed to claim item.');
            }

            return $this->commitReturn($target);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }
}
