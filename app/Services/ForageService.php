<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;
use Settings;

use Carbon\Carbon;

use App\Models\Foraging\Forage;
use App\Models\Foraging\ForageReward;
use App\Models\User\UserForaging;

class ForageService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Loot Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of loot tables.
    |
    */

    /**
     * Creates a loot table.
     *
     * @param  array  $data
     * @return bool|\App\Models\Loot\Forage
     */
    public function createForage($data)
    {
        DB::beginTransaction();

        try {
            
            // More specific validation
            foreach($data['rewardable_type'] as $key => $type)
            {
                if(!$type) throw new \Exception("Loot type is required.");
                if(!$data['rewardable_id'][$key]) throw new \Exception("Reward is required.");
                if(!$data['quantity'][$key] || $data['quantity'][$key] < 1) throw new \Exception("Quantity is required and must be an integer greater than 0.");
                if(!$data['weight'][$key] || $data['weight'][$key] < 1) throw new \Exception("Weight is required and must be an integer greater than 0.");
            }

            if(!isset($data['is_active'])) $data['is_active'] = 0;

            $image = null;
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }
            else $data['has_image'] = 0;

            $table = Forage::create(array_only($data, ['name', 'display_name', 'is_active', 'has_image']));

            if ($image) $this->handleImage($image, $table->imagePath, $table->imageFileName);

            $this->populateForage($table, array_only($data, ['rewardable_type', 'rewardable_id', 'quantity', 'weight']));

            return $this->commitReturn($table);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates a loot table.
     *
     * @param  \App\Models\Loot\Forage  $table
     * @param  array                       $data 
     * @return bool|\App\Models\Loot\Forage
     */
    public function updateForage($table, $data)
    {
        DB::beginTransaction();

        try {
            
            // More specific validation
            foreach($data['rewardable_type'] as $key => $type)
            {
                if(!$type) throw new \Exception("Loot type is required.");
                if(!$data['rewardable_id'][$key]) throw new \Exception("Reward is required.");
                if(!$data['quantity'][$key] || $data['quantity'][$key] < 1) throw new \Exception("Quantity is required and must be an integer greater than 0.");
                if(!$data['weight'][$key] || $data['weight'][$key] < 1) throw new \Exception("Weight is required and must be an integer greater than 0.");
            }

            if(!isset($data['is_active'])) $data['is_active'] = 0;

            $image = null;
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }
            else $data['has_image'] = 0;

            $table->update(array_only($data, ['name', 'display_name', 'is_active', 'has_image']));

            if ($image) $this->handleImage($image, $table->imagePath, $table->imageFileName);

            $this->populateForage($table, array_only($data, ['rewardable_type', 'rewardable_id', 'quantity', 'weight']));

            return $this->commitReturn($table);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Handles the creation of loot for a loot table.
     *
     * @param  \App\Models\Loot\Forage  $table
     * @param  array                       $data 
     */
    private function populateForage($table, $data)
    {
        // Clear the old loot...
        $table->loot()->delete();

        foreach($data['rewardable_type'] as $key => $type)
        {
            ForageReward::create([
                'forage_id'   => $table->id,
                'rewardable_type' => $type,
                'rewardable_id'   => $data['rewardable_id'][$key],
                'quantity'        => $data['quantity'][$key],
                'weight'          => $data['weight'][$key]
            ]);
        }
    }

    /**
     * Deletes a loot table.
     *
     * @param  \App\Models\Loot\Forage  $table
     * @return bool
     */
    public function deleteForage($table)
    {
        DB::beginTransaction();

        try {

            $table->loot()->delete();
            $table->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    // initaliases forage
    public function initForage($id, $user)
    {
        DB::beginTransaction();

        try {
            if(!$user->foraging) {
                $user->foraging->create([
                    'user_id' => $user->id,
                    'stamina' => Settings::get('foraging_stamina'),
                ]);
            }

            if($user->foraging->stamina < 1) throw new \Exception('You have exhausted yourself already! Come back tomorrow.');
            
            $user->foraging->last_forage_id = $id; // set id so we can distribute after an hour
            $user->foraging->last_foraged_at = carbon::now(); // set time, this is useless and just for funsies
            $user->foraging->distribute_at = carbon::now()->addMinutes(60); // set time to allow the user to claim, we can technically calculate this
                                                                            // but i set it up like this so it's staying like this
            $user->foraging->stamina -= 1;
            $user->foraging->save();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Rolls the forage loot table and resets user forage status.
     */
    public function claimReward($user)
    {
        DB::beginTransaction();

        try {

            $forage = Forage::find($user->foraging->last_forage_id);
            if(!$forage) throw new \Exception('Error finding forage.');

            $rewards = $this->processRewards($forage, true);

            $logType = 'Foraging Rewards';
            $data = [
                'data' => 'Received rewards for foraging in '. $forage->display_name . '.'
            ];

            if(!$rewards = fillUserAssets($rewards, $user, $user, $logType, $data)) throw new \Exception("Failed to distribute rewards.");

            $user->foraging->last_forage_id = null;
            $user->foraging->distribute_at = null;
            $user->foraging->save();

            flash($this->getRewardsString($rewards))->success();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Gets the data in a distribution friendly format
     */
    private function processRewards($data, $isStaff = false)
    {
        $assets = createAssetsArray(false);

        addAsset($assets, $data, 1);
                    
        return $assets;
    }

    /**
     * Returns a string of the rewards so the user can see what they have received.
     */
    private function getRewardsString($rewards)
    {
        $results = "You have received: ";
        $result_elements = [];
        foreach($rewards as $assetType)
        {
            if(isset($assetType))
            {
                foreach($assetType as $asset)
                {
                    array_push($result_elements, $asset['asset']->name.(class_basename($asset['asset']) == 'Raffle' ? ' (Raffle Ticket)' : '')." x".$asset['quantity']);
                }
            }
        }
        return $results.implode(', ', $result_elements);
    }
}
