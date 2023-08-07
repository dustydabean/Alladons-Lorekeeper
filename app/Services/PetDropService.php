<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Models\Item\Item;
use App\Models\Currency\Currency;
use App\Models\Loot\LootTable;
use App\Models\Pet\Pet;
use App\Models\Pet\PetVariant;
use App\Models\Pet\PetDropData;
use App\Models\Pet\PetDrop;
use App\Models\User\UserPet;

class PetDropService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Pet Drop Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation, editing and distribution of pet drops.
    |
    */

    /**
     * Creates pet drop data.
     *
     * @param  array  $data
     * @return bool|\App\Models\Pet\PetDropData
     */
    public function createPetDrop($data)
    {
        DB::beginTransaction();

        try {
            // Check to see if pet exists
            $pet = Pet::find($data['pet_id']);
            if(!$pet) throw new \Exception('The selected pet is invalid.');

            // Collect parameter data and encode it
            $paramData = [];
            foreach($data['label'] as $key => $param) $paramData[$param] = $data['weight'][$key];
            $data['parameters'] = json_encode($paramData);

            $data['data']['frequency'] = ['frequency' => $data['drop_frequency'], 'interval' => $data['drop_interval']];
            $data['is_active'] = isset($data['is_active']) && $data['is_active'] ? $data['is_active'] : 0;
            $data['data']['drop_name'] = isset($data['drop_name']) ? $data['drop_name'] : null;
            $data['data'] = json_encode($data['data']);

            $drop = PetDropData::create(Arr::only($data, ['pet_id', 'parameters', 'data']));

            return $this->commitReturn($drop);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates pet drop data.
     *
     * @param  \App\Models\Pet\PetDropData  $drop
     * @param  array                                    $data
     * @return bool|\App\Models\Pet\PetDropData
     */
    public function updatePetDrop($drop, $data)
    {
        DB::beginTransaction();

        try {
            // Check to see if pet exists and if drop data already exists for it.
            $pet = Pet::find($data['pet_id']);
            if(!$pet) throw new \Exception('The selected pet is invalid.');
            if(PetDropData::where('pet_id', $data['pet_id'])->where('id', '!=', $drop->id)->exists()) throw new \Exception('This pet already has drop data. Consider editing the existing data instead.');

            // Collect parameter data and encode it
            $paramData = [];
            foreach($data['label'] as $key => $param) $paramData[$param] = $data['weight'][$key];
            $data['parameters'] = json_encode($paramData);

            // Validate items and process the data if appropriate
            // Process the additional rewards
            $assets = [];
            if (isset($data['rewardable_type']) && $data['rewardable_type']) {
                foreach ($data['rewardable_type'] as $group => $types) {
                    foreach($types as $key=>$type) {
                        if (!isset($assets[$group])) {
                            $assets[$group] = createAssetsArray();
                        }
                        $reward = null;
                        switch ($type) {
                            case 'Item':
                                $reward = Item::find($data['rewardable_id'][$group][$key]);
                                break;
                            case 'Currency':
                                $reward = Currency::find($data['rewardable_id'][$group][$key]);
                                if (!$reward->is_user_owned) {
                                    throw new \Exception('Invalid currency selected.');
                                }
                                break;
                            case 'LootTable':
                                $reward = LootTable::find($data['rewardable_id'][$group][$key]);
                                break;
                        }
                        if (!$reward) {
                            continue;
                        }
                        addDropAsset($assets[$group], $reward, $data['min_quantity'][$group][$key], $data['max_quantity'][$group][$key]);
                    }
                }
            }
            $data['data']['assets'] = getDataReadyDropAssets($assets);

            $data['data']['frequency'] = ['frequency' => $data['drop_frequency'], 'interval' => $data['drop_interval']];
            $data['is_active'] = isset($data['is_active']) && $data['is_active'] ? $data['is_active'] : 0;
            $data['data']['drop_name'] = isset($data['drop_name']) ? $data['drop_name'] : null;
            $data['data']['cap'] = isset($data['cap']) ? $data['cap'] : null;
            $data['data'] = json_encode($data['data']);

            $drop->update(Arr::only($data, ['pet_id', 'parameters', 'data', 'is_active', 'assets']));

            return $this->commitReturn($drop);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Deletes pet drop data.
     *
     * @param  \App\Models\Pet\PetDropData  $drop
     * @return bool
     */
    public function deletePetDrop($drop)
    {
        DB::beginTransaction();

        try {
            // Check first if the table is currently in use
            // - Prompts
            // - Box rewards (unfortunately this can't be checked easily)
            if(PetDrop::where('drop_id', $drop->id)->exists()) throw new \Exception('A pet has drops using this data. Consider disabling drops instead.');

            $drop->petDrops()->delete();
            $drop->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**********************************************************************************************

        DROP CLAIMING

    **********************************************************************************************/

    // yes this should technically be in a manager or similar
    // but it doesnt quiet fit in the pet manager so we use it here!!! yay!!

    /**
     * Claim pet drops and credit user the items from the drop.
     *
     * @param  \App\Models\User\UserPet             $pet
     * @return bool
     */
    public function claimPetDrops(UserPet $pet)
    {
        DB::beginTransaction();

        try {

            if(!$pet->drops->drops_available) throw new \Exception('This pet doesn\'t have any available drops.');
            if(!$pet->drops->dropData->isActive) throw new \Exception('Drops are not currently active for this pet.');

            $rewards = createAssetsArray();
            // these are handled like prompt rewards
            for ($i = 0; $i < $pet->drops->drops_available; $i++) {
                foreach($pet->drops->dropData->rewards(false)[strtolower($pet->drops->parameters)] as $data) {
                    // get object
                    switch ($data->rewardable_type) {
                        case 'Item':
                            $reward = Item::find($data->rewardable_id);
                            break;
                        case 'Currency':
                            $reward = Currency::find($data->rewardable_id);
                            if (!$reward->is_user_owned) {
                                throw new \Exception('Invalid currency selected.');
                            }
                            break;
                        case 'LootTable':
                            $reward = LootTable::find($data->rewardable_id);
                            break;
                    }
                    if (!$reward) {
                        continue;
                    }
                    // get quantity
                    $quantity = mt_rand($data->min_quantity, $data->max_quantity);
                    addAsset($rewards, $reward, $quantity);
                }
            }
            //  TODO: add variants
            if(!fillUserAssets($rewards, null, $pet->user, 'Pet Drop', [
                'data' => 'Collected from '.($pet->pet_name ? $pet->pet_name.' the '.$pet->pet->name : $pet->pet->name ),
                'notes' => 'Collected ' . format_date(Carbon::now())
            ])) throw new \Exception("Failed to distribute drops.");

            // Clear the number of available drops
            $pet->drops->update(['drops_available' => 0]);

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
}
