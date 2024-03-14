<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;
use Settings;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Models\Foraging\Forage;
use App\Models\Foraging\ForageReward;
use App\Models\User\UserForaging;

use App\Models\Character\Character;

use App\Models\Currency\Currency;
use App\Services\CurrencyManager;


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

            if(!isset($data['is_active'])) $data['is_active'] = 0;
            if(!isset($data['stamina_cost'])) $data['stamina_cost'] = 1;

            $image = null;
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }
            else $data['has_image'] = 0;

            if (!isset($data['has_cost']) || !$data['has_cost']) {
                // set currency_id and currency_quantity to null
                $data['currency_id'] = null;
                $data['currency_quantity'] = null;
            }

            if ($data['currency_id'] == 'None') {
                $data['has_cost'] = 0;
                $data['currency_id'] = null;
                $data['currency_quantity'] = null;
            }

            $table = Forage::create(array_only($data, ['name', 'display_name', 'is_active', 'has_image', 'stamina_cost', 'active_until', 'has_cost', 'currency_id', 'currency_quantity']));

            if ($image) $this->handleImage($image, $table->imagePath, $table->imageFileName);

            if (isset($data['rewardable_type'])) $this->populateForage($table, Arr::only($data, ['rewardable_type', 'rewardable_id', 'quantity', 'weight']));

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

            if(!isset($data['is_active'])) $data['is_active'] = 0;
            if(!isset($data['stamina_cost'])) $data['stamina_cost'] = 1;

            $image = null;
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }
            else $data['has_image'] = 0;

            if (!isset($data['has_cost']) || !$data['has_cost']) {
                // set currency_id and currency_quantity to null
                $data['currency_id'] = null;
                $data['currency_quantity'] = null;
            }

            if ($data['currency_id'] == 'None') {
                $data['has_cost'] = 0;
                $data['currency_id'] = null;
                $data['currency_quantity'] = null;
            }

            if ($table->has_image && !$image) {
                $data['has_image'] = 1;
            }

            $table->update(Arr::only($data, ['name', 'display_name', 'is_active', 'has_image', 'stamina_cost', 'active_until', 'has_cost', 'currency_id', 'currency_quantity']));

            if ($image) $this->handleImage($image, $table->imagePath, $table->imageFileName);

            if (isset($data['rewardable_type'])) $this->populateForage($table, Arr::only($data, ['rewardable_type', 'rewardable_id', 'quantity', 'weight']));

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
        foreach($data['rewardable_type'] as $key => $type)
        {
            if(!$type) throw new \Exception("Loot type is required.");
            if(!$data['rewardable_id'][$key]) throw new \Exception("Reward is required.");
            if(!$data['quantity'][$key] || $data['quantity'][$key] < 1) throw new \Exception("Quantity is required and must be an integer greater than 0.");
            if(!$data['weight'][$key] || $data['weight'][$key] < 1) throw new \Exception("Weight is required and must be an integer greater than 0.");
        }

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
            // get all users with this forage
            $users = UserForaging::where('forage_id', $table->id)->get();
            foreach($users as $user) {
                $user->forage_id = null;
                $user->distribute_at = null;
                $user->save();
            }
            $table->loot()->delete();
            $table->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /*****************************************************************************************
     * FORAGING RELATED FUNCTIONS
     *****************************************************************************************/

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

            // check if a distribute_at already exists
            if ($user->foraging->forage_id && $user->foraging->distribute_at) throw new \Exception('You have already begun foraging.');

            // check if the forage has a cost and decrement currency accordingly
            $forage = Forage::findOrFail($id);
            if ($forage->has_cost && $forage->currency_id && $forage->currency_quantity > 0) {
                if(!(new CurrencyManager)->debitCurrency($user, null, 'Foraging Cost', 'Cost to Forage in the ' . $forage->display_name, Currency::find($forage->currency_id), $forage->currency_quantity))
                    throw new \Exception('Could not debit currency.');
            }
            if (!$forage->loot()->count()) throw new \Exception('This forage has no rewards.');
            if(config('lorekeeper.foraging.use_characters') && !$user->foraging->character_id) throw new \Exception('Please select a character.');

            $user->foraging->forage_id = $id;
            $user->foraging->foraged_at = carbon::now();
            $user->foraging->distribute_at = carbon::now()->addMinutes(config('lorekeeper.foraging.forage_time'));

            // CHARACTER_STAMINA_DECREMENT
            if(config('lorekeeper.foraging.use_characters')) {
                if (config('lorekeeper.foraging.use_foraging_stamina')) {
                    $user->foraging->stamina -= $user->foraging->forage->stamina_cost;
                    $user->foraging->save();
                }
                else {
                    // put in stamina stat shit here if you want
                    throw new \Exception('Please ensure the decrement stamina area has been appropriately edited.');
                }
            }
            else {
                // USER_STAMINA_DECREMENT
                if (config('lorekeeper.foraging.use_foraging_stamina')) {
                    $user->foraging->stamina -= $user->foraging->forage->stamina_cost;
                    $user->foraging->save();
                }
                else {
                    // if you dont want a forage to take stamina set the stamina to 0 on the edit page
                    // dont use this here
                    throw new \Exception('Please ensure the decrement stamina area has been appropriately edited.');
                }
            }

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

            $forage = $user->foraging->forage;
            if(!$forage) throw new \Exception('Error finding forage.');

            if (!$user->foraging->forage_id) throw new \Exception('You have not started a forage yet.');
            if (!$user->foraging->distribute_at) throw new \Exception('You have not started a forage yet.');
            if (!$user->foraging->forage->loot()->count()) throw new \Exception('This forage has no rewards.');
            // check it's been forage_time since forage started
            $now = Carbon::now();
            // add forage time to foraged_at
            $distribute = $user->foraging->foraged_at->addMinutes(config('lorekeeper.foraging.forage_time'));
            if($now->lt($distribute)) throw new \Exception('You must wait until the forage is complete before claiming your rewards.');
            // check distribute_at also
            $distribute = $user->foraging->distribute_at;
            if($now->lt($distribute)) throw new \Exception('You must wait until the forage is complete before claiming your rewards.');

            $rewards = $this->processRewards($forage, true);

            $logType = 'Foraging Rewards';
            $data = [
                'data' => 'Received rewards for foraging in '. $forage->display_name . '.'
            ];

            if(!$rewards = fillUserAssets($rewards, $user, $user, $logType, $data)) throw new \Exception("Failed to distribute rewards.");

            $user->foraging->forage_id = null;
            $user->foraging->distribute_at = null; // we can check against this to stop multi window claims
            $user->foraging->save();

            if(config('lorekeeper.foraging.use_characters')) $name = $user->foraging->character->fullName;

            flash($this->getRewardsString($rewards, $name ?? null))->success();

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
    private function getRewardsString($rewards, $character_name = null)
    {
        if(config('lorekeeper.foraging.use_characters'))
            $results = $character_name . " has found: ";
        else $results = "You have received: ";
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

    /**
     * Selects character for dungeon.
     *
     * @param int $id
     *
     * @return int $user->foraging->character_id
     */
    public function editSelectedCharacter($user, $id) {
        DB::beginTransaction();

        try {
            if (!$id) {
                throw new \Exception('Please select a character.');
            }
            $character = Character::find($id);
            if (!$character) {
                throw new \Exception('Invalid character.');
            }
            if ($character->user_id != $user->id && !config('lorekeeper.foraging.npcs.enabled')) {
                throw new \Exception('You are not this characters owner.');
            }

            $user->foraging->character_id = $id;
            $user->foraging->save();

            return $this->commitReturn($user);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }
}
