<?php namespace App\Services;

use Carbon\Carbon;
use App\Services\Service;

use DB;
use Notifications;
use Config;

use App\Models\User\User;
use App\Models\User\UserItem;
use App\Models\User\UserCollection;

use App\Models\Collection\Collection;
use App\Models\Collection\CollectionIngredient;
use App\Models\Collection\CollectionReward;

use App\Services\InventoryManager;

class CollectionService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Collection Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of collections.
    |
    */

    /**********************************************************************************************
     
        CollectionS
    **********************************************************************************************/

    /**
     * Creates a new collection.
     *
     * @param  array                  $data 
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Collection\Collection
     */
    public function createCollection($data, $user)
    {
        DB::beginTransaction();

        try {
                        if(!isset($data['ingredient_type'])) throw new \Exception('Please add at least one ingredient.');
            if(!isset($data['rewardable_type'])) throw new \Exception('Please add at least one reward to the collection.');

            $data = $this->populateData($data);

            foreach($data['ingredient_type'] as $key => $type)
            {
                if(!$type) throw new \Exception("Ingredient type is required.");
                if(!$data['ingredient_data'][$key]) throw new \Exception("Ingredient data is required.");
                if(!$data['ingredient_quantity'][$key] || $data['ingredient_quantity'][$key] < 1) throw new \Exception("Quantity is required and must be an integer greater than 0.");
            }

            foreach($data['rewardable_type'] as $key => $type)
            {
                if(!$type) throw new \Exception("Reward type is required.");
                if(!$data['rewardable_id'][$key]) throw new \Exception("Reward is required.");
                if(!$data['reward_quantity'][$key] || $data['reward_quantity'][$key] < 1) throw new \Exception("Quantity is required and must be an integer greater than 0.");
            }

            $image = null;
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }
            else $data['has_image'] = 0;

            $collection = Collection::create($data);
            $this->populateIngredients($collection, $data);


            $collection->output = $this->populateRewards($data);
            $collection->save();

            if ($image) $this->handleImage($image, $collection->imagePath, $collection->imageFileName);

            return $this->commitReturn($collection);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates an collection.
     *
     * @param  \App\Models\Collection\Collection  $collection
     * @param  array                  $data 
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Collection\Collection
     */
    public function updateCollection($collection, $data, $user)
    {
        DB::beginTransaction();

        try {
           

            // More specific validation
            if(Collection::where('name', $data['name'])->where('id', '!=', $collection->id)->exists()) throw new \Exception("The name has already been taken.");
            
            if(!isset($data['ingredient_type'])) throw new \Exception('Please add at least one ingredient.');
            if(!isset($data['rewardable_type'])) throw new \Exception('Please add at least one reward to the collection.');

            $data = $this->populateData($data);
            $this->populateIngredients($collection, $data);


            $image = null;            
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }

            $collection->update($data);
            $collection->output = $this->populateRewards($data);
            $collection->save();

            if ($collection) $this->handleImage($image, $collection->imagePath, $collection->imageFileName);

            return $this->commitReturn($collection);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Processes user input for creating/updating an collection.
     *
     * @param  array                  $data 
     * @param  \App\Models\Collection\Collection  $collection
     * @return array
     */
    private function populateData($data, $collection = null)
    {
        if(isset($data['description']) && $data['description']) $data['parsed_description'] = parse($data['description']);

        if(isset($data['needs_unlocking']) && $data['needs_unlocking']) $data['needs_unlocking'] = 1;
        else $data['needs_unlocking'] = 0;

        if(isset($data['remove_image']))
        {
            if($collection && $collection->has_image && $data['remove_image']) 
            { 
                $data['has_image'] = 0; 
                $this->deleteImage($collection->imagePath, $collection->imageFileName); 
            }
            unset($data['remove_image']);
        }

        return $data;
    }

    /**
     * Manages ingredients attached to the collection
     *
     * @param  \App\Models\Collection\Collection   $collection
     * @param  array                       $data 
     */
    private function populateIngredients($collection, $data)
    {
        $collection->ingredients()->delete();

        foreach($data['ingredient_type'] as $key => $type)
        {
            if(!count(array_filter($data['ingredient_data'][$key]))) throw new \Exception('One of the ingredients was not specified.');
            CollectionIngredient::create([
                'collection_id' => $collection->id,
                'ingredient_type' => $type,
                'ingredient_data' => json_encode($data['ingredient_data'][$key]),
                'quantity' => $data['ingredient_quantity'][$key]
            ]);
        }
    }

    /**
     * Creates the assets json from rewards
     *
     * @param  \App\Models\Collection\Collection   $collection
     * @param  array                       $data 
     */
    private function populateRewards($data)
    {
        if(isset($data['rewardable_type'])) {
            // The data will be stored as an asset table, json_encode()d. 
            // First build the asset table, then prepare it for storage.
            $assets = createAssetsArray();
            foreach($data['rewardable_type'] as $key => $r) {
                switch ($r)
                {
                    case 'Item':
                        $type = 'App\Models\Item\Item';
                        break;
                    case 'Currency':
                        $type = 'App\Models\Currency\Currency';
                        break;
                    case 'LootTable':
                        $type = 'App\Models\Loot\LootTable';
                        break;
                    case 'Raffle':
                        $type = 'App\Models\Raffle\Raffle';
                        break;
                }
                $asset = $type::find($data['rewardable_id'][$key]);
                addAsset($assets, $asset, $data['reward_quantity'][$key]);
            }

            return getDataReadyAssets($assets);
        }
        return null;
    }


    /**
     * Deletes an collection.
     *
     * @param  \App\Models\Collection\Collection  $collection
     * @return bool
     */
    public function deleteCollection($collection)
    {
        DB::beginTransaction();

        try {
            // Check first if the collection is currently owned or if some other site feature uses it
            if(DB::table('user_collections')->where('collection_id', $collection->id)->exists()) throw new \Exception("At least one user currently owns this collection. Please remove the collection(s) before deleting it.");
            if(DB::table('loots')->where('rewardable_type', 'Collection')->where('rewardable_id', $collection->id)->exists()) throw new \Exception("A loot table currently distributes this collection as a potential reward. Please remove the collection before deleting it.");
            if(DB::table('prompt_rewards')->where('rewardable_type', 'Collection')->where('rewardable_id', $collection->id)->exists()) throw new \Exception("A prompt currently distributes this collection as a reward. Please remove the collection before deleting it.");
            // FIXME if(DB::table('shop_stock')->where('collection_id', $collection->id)->exists()) throw new \Exception("A shop currently stocks this collection. Please remove the collection before deleting it.");

            DB::table('user_collections_log')->where('collection_id', $collection->id)->delete();
            DB::table('user_collections')->where('collection_id', $collection->id)->delete();
            // FIXME $collection->tags()->delete();
            if($collection->has_image) $this->deleteImage($collection->imagePath, $collection->imageFileName); 
            $collection->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }



    // my brain is too small to think of a way to keep users from completing a collection only once so we're "granting" one when a user completes a collection


        /**
     * Credits collection to a user or character.
     *
     * @param  \App\Models\User\User                        $sender
     * @param  \App\Models\User\User                        $recipient
     * @param  \App\Models\Character\Character              $character
     * @param  string                                       $type 
     * @param  string                                       $data
     * @param  \App\Models\Collection\Collection                    $collection
     * @param  int                                          $quantity
     * @return  bool
     */
    public function creditCollection($sender, $recipient, $character, $type, $data, $collection)
    {
        DB::beginTransaction();

        try {
            if(is_numeric($collection)) $collection = Collection::find($collection);

            if($recipient->collections->contains($collection)) {
                flash($recipient->name." already completed the collection ".$collection->displayName, 'warning');
                return $this->commitReturn(false);
            }

            $record = UserCollection::where('user_id', $recipient->id)->where('collection_id', $collection->id)->first();
            if($record) {
                // Laravel doesn't support composite primary keys, so directly updating the DB row here
                DB::table('user_collections')->where('user_id', $recipient->id)->where('collection_id', $collection->id);
            }
            else {
                $record = UserCollection::create(['user_id' => $recipient->id, 'collection_id' => $collection->id]);
            }

            if($type && !$this->createLog($sender ? $sender->id : null, $recipient ? $recipient->id : null,
            $character ? $character->id : null, $type, $data['data'], $collection->id)) throw new \Exception("Failed to create log.");

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    
    /**
     * Creates an collection log.
     *
     * @param  int     $senderId
     * @param  string  $senderType
     * @param  int     $recipientId
     * @param  string  $recipientType
     * @param  int     $userCollectionId
     * @param  string  $type 
     * @param  string  $data
     * @param  int     $collectionId
     * @return  int
     */
    public function createLog($senderId, $recipientId, $characterId, $type, $data, $collectionId)
    {
        return DB::table('user_collections_log')->insert(
            [
                'sender_id' => $senderId,
                'recipient_id' => $recipientId,
                'character_id' => $characterId,
                'collection_id' => $collectionId,
                'log' => $type . ($data ? ' (' . $data . ')' : ''),
                'log_type' => $type,
                'data' => $data, // this should be just a string
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        );
    }
}

