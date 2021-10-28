<?php namespace App\Services\Item;

use App\Services\Service;

use DB;
use Settings;

use App\Services\InventoryManager;
use App\Services\CurrencyManager;

use App\Models\Character\Character;
use App\Models\Currency\Currency;

class BreedingpermService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Box Service
    |--------------------------------------------------------------------------
    |
    | Handles the editing and usage of box type items.
    |
    */

    /**
     * Retrieves any data that should be used in the item tag editing form.
     *
     * @return array
     */
    public function getEditData()
    {
        return [];
    }

    /**
     * Processes the data attribute of the tag and returns it in the preferred format.
     *
     * @param  string  $tag
     * @return mixed
     */
    public function getTagData($tag)
    {
        return $tag->data;
    }

    /**
     * Processes the data attribute of the tag and returns it in the preferred format.
     *
     * @param  string  $tag
     * @param  array   $data
     * @return bool
     */
    public function updateData($tag, $data)
    {
        DB::beginTransaction();

        try {
            $tag->update(['data' => $data['quantity']]);

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }


    /**
     * Acts upon the item when used from the inventory.
     *
     * @param  \App\Models\User\UserItem  $stacks
     * @param  \App\Models\User\User      $user
     * @param  array                      $data
     * @return bool
     */
    public function act($stacks, $user, $data)
    {
        DB::beginTransaction();

        try {
            foreach($stacks as $key=>$stack) {
                // We don't want to let anyone who isn't the owner of the box open it,
                // so do some validation...
                if($stack->user_id != $user->id) throw new \Exception("This item does not belong to you.");

                $character = Character::where('id', $data['breedingperm_character_id'])->first();
                if(!$character) throw new \Exception('Invalid character selected.');

                // Next, try to delete the box item. If successful, we can start distributing rewards.
                if((new InventoryManager)->debitStack($stack->user, 'Breeding Permission'.$stack->item->tag('breedingperm')->data == 1 ? '' : 's'.' Redeemed', ['data' => ''], $stack, $data['quantities'][$key])) {

                    for($q=0; $q<$data['quantities'][$key]; $q++) {
                        // Distribute character rewards
                        if(!(new CurrencyManager)->creditCurrency($user, $character, 'Redeemed Breeding Permission'.($stack->item->tag('breedingperm')->data == 1 ? '' : 's'), 'Received from using '.$stack->item->name, Settings::get('breeding_permission_currency'), $stack->item->tag('breedingperm')->data)) throw new \Exception("Failed to redeem breeding permissions.");
                    }
                }
            }
            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
}
