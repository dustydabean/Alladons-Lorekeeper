<?php

namespace App\Services;

use App\Facades\Notifications;
use App\Facades\Settings;
use App\Models\Character\Character;
use App\Models\Pet\Pet;
use App\Models\Pet\PetDrop;
use App\Models\User\User;
use App\Models\User\UserItem;
use App\Models\User\UserPet;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PetManager extends Service {
    /*
    |--------------------------------------------------------------------------
    | Inventory Manager
    |--------------------------------------------------------------------------
    |
    | Handles modification of user-owned pets.
    |
    */

    /**
     * Grants an pet to multiple users.
     *
     * @param array $data
     * @param User  $staff
     *
     * @return bool
     */
    public function grantPets($data, $staff) {
        DB::beginTransaction();

        try {
            foreach ($data['quantities'] as $q) {
                if ($q <= 0) {
                    throw new \Exception('All quantities must be at least 1.');
                }
            }

            // Process names
            $users = User::find($data['names']);
            if (count($users) != count($data['names'])) {
                throw new \Exception('An invalid user was selected.');
            }

            $keyed_quantities = [];
            array_walk($data['pet_ids'], function ($id, $key) use (&$keyed_quantities, $data) {
                if ($id != null && !in_array($id, array_keys($keyed_quantities), true)) {
                    $keyed_quantities[$id] = $data['quantities'][$key];
                }
            });

            $keyed_variant = [];
            array_walk($data['pet_ids'], function ($id, $key) use (&$keyed_variant, $data) {
                if (isset($data['variant'])) {
                    if ($id != null && !in_array($id, array_keys($keyed_variant), true)) {
                        $keyed_variant[$id] = $data['variant'][$key];
                    }
                }
            });

            $keyed_evolution = [];
            array_walk($data['pet_ids'], function ($id, $key) use (&$keyed_evolution, $data) {
                if (isset($data['evolution'])) {
                    if ($id != null && !in_array($id, array_keys($keyed_evolution), true)) {
                        $keyed_evolution[$id] = $data['evolution'][$key];
                    }
                }
            });

            // Process pet
            $pets = Pet::find($data['pet_ids']);
            if (!count($pets)) {
                throw new \Exception('No valid companions found.');
            }

            foreach ($users as $user) {
                foreach ($pets as $pet) {
                    if ($this->creditPet($staff, $user, 'Staff Grant', Arr::only($data, ['data', 'disallow_transfer', 'notes']), $pet, $keyed_quantities[$pet->id] ?? 1, $keyed_variant[$pet->id] ?? null, $keyed_evolution[$pet->id] ?? null)) {
                        Notifications::create('PET_GRANT', $user, [
                            'pet_name'     => $pet->name,
                            'pet_quantity' => $keyed_quantities[$pet->id],
                            'sender_url'   => $staff->url,
                            'sender_name'  => $staff->name,
                        ]);
                    } else {
                        throw new \Exception('Failed to credit companions to '.$user->name.'.');
                    }
                }
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Transfers an pet stack between users.
     *
     * @param User    $sender
     * @param User    $recipient
     * @param UserPet $stack
     *
     * @return bool
     */
    public function transferStack($sender, $recipient, $stack) {
        DB::beginTransaction();

        try {
            if (!$sender->hasAlias) {
                throw new \Exception('Your deviantART account must be verified before you can perform this action.');
            }
            if (!$stack) {
                throw new \Exception('Invalid companion selected.');
            }
            if ($stack->user_id != $sender->id && !$sender->hasPower('edit_inventories')) {
                throw new \Exception('You do not own this companion.');
            }
            if ($stack->user_id == $recipient->id) {
                throw new \Exception("Cannot send a companion to the companion's owner.");
            }
            if (!$recipient) {
                throw new \Exception('Invalid recipient selected.');
            }
            if (!$recipient->hasAlias) {
                throw new \Exception('Cannot transfer companions to a non-verified member.');
            }
            if ($recipient->is_banned) {
                throw new \Exception('Cannot transfer companions to a banned member.');
            }
            if ((!$stack->pet->allow_transfer || isset($stack->data['disallow_transfer'])) && !$sender->hasPower('edit_inventories')) {
                throw new \Exception('This companion cannot be transferred.');
            }

            $oldUser = $stack->user;
            if ($this->moveStack($stack->user, $recipient, ($stack->user_id == $sender->id ? 'User Transfer' : 'Staff Transfer'), ['data' => ($stack->user_id != $sender->id ? 'Transferred by '.$sender->displayName : '')], $stack)) {
                Notifications::create('PET_TRANSFER', $recipient, [
                    'pet_name'     => $stack->pet->name,
                    'pet_quantity' => 1,
                    'sender_url'   => $sender->url,
                    'sender_name'  => $sender->name,
                ]);
                if ($stack->user_id != $sender->id) {
                    Notifications::create('FORCED_PET_TRANSFER', $oldUser, [
                        'pet_name'     => $stack->pet->name,
                        'pet_quantity' => 1,
                        'sender_url'   => $sender->url,
                        'sender_name'  => $sender->name,
                    ]);
                }

                return $this->commitReturn(true);
            }
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Deletes an pet stack.
     *
     * @param User    $user
     * @param UserPet $stack
     *
     * @return bool
     */
    public function deleteStack($user, $stack) {
        DB::beginTransaction();

        try {
            if (!$user->hasAlias) {
                throw new \Exception('Your deviantART account must be verified before you can perform this action.');
            }
            if (!$stack) {
                throw new \Exception('Invalid companion selected.');
            }
            if ($stack->user_id != $user->id && !$user->hasPower('edit_inventories')) {
                throw new \Exception('You do not own this companion.');
            }

            $oldUser = $stack->user;

            if ($this->debitStack($stack->user, ($stack->user_id == $user->id ? 'User Deleted' : 'Staff Deleted'), ['data' => ($stack->user_id != $user->id ? 'Deleted by '.$user->displayName : '')], $stack)) {
                if ($stack->user_id != $user->id) {
                    Notifications::create('PET_REMOVAL', $oldUser, [
                        'pet_name'     => $stack->pet->name,
                        'pet_quantity' => 1,
                        'sender_url'   => $user->url,
                        'sender_name'  => $user->name,
                    ]);
                }

                return $this->commitReturn(true);
            }
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Names a pet stack.
     *
     * @param  UserPet
     * @param mixed $pet
     * @param mixed $name
     *
     * @return bool
     */
    public function nameStack($pet, $name) {
        DB::beginTransaction();

        try {
            $user = Auth::user();
            if (!$user->hasAlias) {
                throw new \Exception('Your account must be verified before you can perform this action.');
            }
            if (!$pet) {
                throw new \Exception('An invalid companion was selected.');
            }
            if ($pet->user_id != $user->id && !$user->hasPower('edit_inventories')) {
                throw new \Exception('You do not own this companion.');
            }

            $pet['pet_name'] = $name;
            $pet->save();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Attaches a pet stack.
     *
     * @param mixed $pet
     * @param mixed $id
     *
     * @return bool
     */
    public function attachStack($pet, $id) {
        DB::beginTransaction();

        try {
            // First, check user permissions
            $user = Auth::user();
            // Next, why bother checking everything else if the pet isn't even attachable? Also determine if the user is the owner of the pet/has permission to attach.
            if (!$pet) {
                throw new \Exception('An invalid companion was selected.');
            }
            if ($pet->pet->category && !$pet->pet->category->allow_attach) {
                throw new \Exception('This companion is in a category that cannot be attached to a character.');
            }
            if ($pet->user_id != $user->id && !$user->hasPower('edit_inventories')) {
                throw new \Exception('You do not own this companion.');
            }

            $this->checkCooldown($pet, $user);

            // Next, check if the character the pet is being attached to is valid and the user has permission to attach the pet to that character.
            if (!$id) {
                throw new \Exception('No character selected.');
            }
            $character = Character::find($id);
            if (!$character) {
                throw new \Exception('An invalid character was selected.');
            }
            if ($character->user_id != $user->id && !$user->hasPower('edit_inventories')) {
                throw new \Exception('You do not own this character.');
            }
            if ($character->user_id != $pet->user_id && !$user->hasPower('edit_inventories')) {
                throw new \Exception('This character does not belong to the owner of the pet.');
            }
            if (config('lorekeeper.pets.max_pets') && $character->pets->count() >= config('lorekeeper.pets.max_pets')) {
                throw new \Exception('This character has reached the limit of pets.');
            }

            // Finally, compare character and limits based on pet and pet category.
            $allPets = $character->pets;
            if ($pet->pet->category) {
                $petCategory = $pet->pet->category;
                $categoryLimit = $petCategory->limit;
                $categoryCount = 0;
                foreach ($allPets as $p) {
                    if ($p->pet->pet_category_id == $petCategory->id) {
                        $categoryCount++;
                    }
                }
                if ($categoryLimit && $categoryCount >= $categoryLimit) {
                    throw new \Exception('This character has reached the limit of companions in this category.');
                }
            }
            if ($pet->pet->limit) {
                $petLimit = $pet->pet->limit;
                $petCount = 0;
                foreach ($allPets as $p) {
                    if ($p->pet_id == $pet->pet->id) {
                        $petCount++;
                    }
                }
                if ($petLimit && $petCount >= $petLimit) {
                    throw new \Exception('This character has reached the limit of this companion.');
                }
            }
            $logType = 'Companion Attached';
            $logData = 'Attached '.$pet->fullName.' to '.$character->displayName.' on '.Carbon::now()->format('M j, Y H:i');

            // If all checks pass, attach the pet to the character.
            $pet->character_id = $character->id;
            $pet->attached_at = Carbon::now();
            $pet->save();
            if (!$this->createLog($user->id, null, $pet->id, $logType, $logData, $pet->pet->id ?? null, 1)) {
                throw new \Exception('Failed to create companion attachment log.');
            }

            $pet->ensureLevel();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * detaches a pet stack.
     *
     * @param mixed $pet
     */
    public function detachStack($pet) {
        DB::beginTransaction();

        try {
            $user = Auth::user();
            if (!$user->hasAlias) {
                throw new \Exception('Your deviantART account must be verified before you can perform this action.');
            }
            if (!$pet) {
                throw new \Exception('An invalid companion was selected.');
            }
            if ($pet->user_id != $user->id && !$user->hasPower('edit_inventories')) {
                throw new \Exception('You do not own this companion.');
            }

            $this->checkCooldown($pet, $user);

            $logType = 'Companion Detached';
            $logData = 'Detached '.$pet->fullName.' from '.($pet->character->displayName ?? '???').' on '.Carbon::now()->format('M j, Y H:i');

            $pet['character_id'] = null;
            $pet->save();

            if (!$this->createLog($user->id, null, $pet->id, $logType, $logData, $pet->pet->id ?? null, 1)) {
                throw new \Exception('Failed to create companion detachment log.');
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Bonds with a pet.
     *
     * @param mixed $pet
     * @param mixed $user
     */
    public function bondPet($pet, $user) {
        DB::beginTransaction();

        try {
            if (!config('lorekeeper.pets.pet_bonding_enabled')) {
                throw new \Exception('Companion bonding is not enabled.');
            }

            if ($user->id != $pet->user_id) {
                throw new \Exception('You do not own this companion.');
            }

            if (!$pet->canBond()) {
                throw new \Exception('You cannot bond with this companion again yet.');
            }

            $pet->bonded_at = Carbon::now();
            $pet->save();

            $pet->ensureLevel();

            $pet->level->bonding += 1;
            $pet->level->save();

            $this->processLevelChange($pet);

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Edits variant.
     *
     * @param mixed $id
     * @param mixed $pet
     * @param mixed $stack_id
     * @param mixed $isStaff
     */
    public function editVariant($id, $pet, $stack_id, $isStaff = false) {
        DB::beginTransaction();

        try {
            if (!$isStaff || !Auth::user()->isStaff) {
                if (!$stack_id) {
                    throw new \Exception('No item selected.');
                }

                if ($id == 0) {
                    $id = 'default';
                }

                // check if user has item
                $item = UserItem::find($stack_id);
                $tag = $item->item->tags->where('tag', 'splice')->first();
                if (!$tag) {
                    throw new \Exception('Item is not a splice.');
                }
                if ((isset($tag->data['variant_ids']) && $tag->data['splice_type'] == 'by_variants') && !in_array($id, $tag->data['variant_ids'])) {
                    throw new \Exception('Item is not a splice for this variant.');
                }
                if ($id == $pet->pet_id) {
                    throw new \Exception('Pet is already this variant.');
                }

                $service = new InventoryManager;
                if (!$service->debitStack($pet->user, 'Used to change pet variant', ['data' => 'Used to change '.$pet->pet->name.' variant'], $item, 1)) {
                    foreach ($service->errors()->getMessages()['error'] as $error) {
                        flash($error)->error();
                    }
                    throw new \Exception('Could not debit item.');
                }
            } else {
                $this->logAdminAction($pet->user, 'Pet Variant Changed', json_encode(['pet' => $pet->id, 'variant' => $id]));
            }

            if ($id == 'default' || $id == 0 || $id == '0') {
                // Revert to the base species: parent pet if currently a variant, otherwise keep as-is.
                $pet->pet_id = $pet->pet->isVariant ? $pet->pet->parent_id : $pet->pet_id;
            } else {
                $pet->pet_id = $id;
            }
            $pet->save();

            $pet->load('pet');
            $pet->ensureDrop();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Edits evolution.
     *
     * @param mixed $id
     * @param mixed $pet
     * @param mixed $stack_id
     * @param mixed $isStaff
     */
    public function editEvolution($id, $pet, $stack_id, $isStaff = false) {
        DB::beginTransaction();

        try {
            if (!$isStaff || !Auth::user()->isStaff) {
                if (!$stack_id) {
                    throw new \Exception('No item selected.');
                }

                // check if user has item
                $item = UserItem::find($stack_id);
                $service = new InventoryManager;
                if (!$service->debitStack($pet->user, 'Used to change pet evolution', ['data' => 'Used to change '.$pet->pet->name.' evolution'], $item, 1)) {
                    foreach ($service->errors()->getMessages()['error'] as $error) {
                        flash($error)->error();
                    }

                    throw new \Exception('Could not debit item.');
                }
            } else {
                $this->logAdminAction($pet->user, 'Pet Evolution Changed', json_encode(['pet' => $pet->id, 'evolution' => $id]));
            }

            $pet->evolution_id = $id;
            $pet->save();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Edits the custom image on a user pet stack.
     *
     * @param mixed $pet
     * @param mixed $data
     */
    public function editCustomImage($pet, $data) {
        DB::beginTransaction();

        try {
            $data['has_image'] = 1;
            $image = null;
            if (isset($data['remove_image'])) {
                if ($pet && $pet->has_image && $data['remove_image']) {
                    $data['has_image'] = 0;
                    if (file_exists($pet->imagePath.'/'.$pet->imageFileName)) {
                        $this->deleteImage($pet->imagePath, $pet->imageFileName);
                    }
                }
                unset($data['remove_image']);
                unset($data['image']);
                $data['has_image'] = 0;
            }

            if (isset($data['image']) && $data['image']) {
                $image = $data['image'];
                unset($data['image']);
                $data['has_image'] = 1;
            }

            $data['artist_id'] = (isset($data['remove_credit']) && $data['remove_credit']) ? null : ($data['artist_id'] ?? null);
            $data['artist_url'] = (isset($data['remove_credit']) && $data['remove_credit']) ? null : ($data['artist_url'] ?? null);

            $pet->update($data);

            if ($pet) {
                $this->handleImage($image, $pet->imagePath, $pet->imageFileName);
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Change pet's description.
     *
     * @param mixed $pet
     * @param mixed $data
     */
    public function editCustomImageDescription($pet, $data) {
        DB::beginTransaction();

        try {
            $pet->description = parse($data['description']);
            $pet->save();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Credits an pet to a user.
     *
     * @param User       $sender
     * @param User       $recipient
     * @param string     $type
     * @param array      $data
     * @param Pet        $pet
     * @param int        $quantity
     * @param mixed      $variant_id
     * @param mixed|null $evolution_id
     *
     * @return bool
     */
    public function creditPet($sender, $recipient, $type, $data, $pet, $quantity, $variant_id = null, $evolution_id = null) {
        DB::beginTransaction();

        try {
            for ($i = 0; $i < $quantity; $i++) {
                if ($variant_id == 'randomize' && count($pet->variants)) {
                    // randomly get a variant
                    $variant = $pet->variants->random();
                    // 25% chance to be no variant
                    if (rand(1, 4) == 1) {
                        $variant = null;
                    }
                } elseif ($variant_id == 'none') {
                    $variant = null;
                } else {
                    $variant = $pet->variants->where('id', $variant_id)->first();
                }

                if ($evolution_id == 'randomize' && count($pet->evolutions)) {
                    // randomly get an evolution
                    $evolution = $pet->evolutions->random();
                    // 25% chance to be no evolution
                    if (rand(1, 4) == 1) {
                        $evolution = null;
                    }
                } elseif ($evolution_id == 'none') {
                    $evolution = null;
                } else {
                    $evolution = $pet->evolutions->where('id', $evolution_id)->first();
                }

                $user_pet = UserPet::create([
                    'user_id'      => $recipient->id,
                    'pet_id'       => $variant ? $variant->id : $pet->id,
                    'data'         => $data,
                    'evolution_id' => $evolution?->id,
                ]);

                if ($user_pet) {
                    $user_pet->ensureLevel();
                }

                // Create drop information for the pet, if relevant
                if ($variant ? ($user_pet->pet->hasDrops || $user_pet->pet->parent->hasDrops) : $user_pet->pet->hasDrops) {
                    if ($variant) {
                        $variantDrops = $variant->hasDrops ?? null;
                    } else {
                        $variantDrops = null;
                    }
                    $nextDayFrequency = $variant ? ($variantDrops ? $user_pet->pet->dropData->frequency : $user_pet->pet->parent->dropData->frequency) : $user_pet->pet->dropData->frequency;
                    $nextDayInterval = $variant ? ($variantDrops ? $user_pet->pet->dropData->interval : $user_pet->pet->parent->dropData->interval) : $user_pet->pet->dropData->interval;

                    $drop = PetDrop::create([
                        'drop_id'         => $variant ? ($variantDrops ? $user_pet->pet->dropData->id : $user_pet->pet->parent->dropData->id) : $user_pet->pet->dropData->id,
                        'user_pet_id'     => $user_pet->id,
                        'parameters'      => $variant ? ($variantDrops ? $user_pet->pet->dropData->rollParameters() : $user_pet->pet->parent->dropData->rollParameters()) : $user_pet->pet->dropData->rollParameters(),
                        'drops_available' => 0,
                        'next_day'        => Carbon::now()
                            ->add($nextDayFrequency, $nextDayInterval)
                            ->startOf($nextDayInterval),
                    ]);

                    if (!$drop) {
                        throw new \Exception('Failed to create drop.');
                    }
                }
            }

            if ($type && !$this->createLog($sender ? $sender->id : null, $recipient->id, null, $type, $data['data'], $pet->id, $quantity)) {
                throw new \Exception('Failed to create log.');
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Moves an pet stack from one user to another.
     *
     * @param User   $sender
     * @param User   $recipient
     * @param string $type
     * @param array  $data
     * @param mixed  $stack
     *
     * @return bool
     */
    public function moveStack($sender, $recipient, $type, $data, $stack) {
        DB::beginTransaction();

        try {
            $cooldown = Settings::get('pet_transfer_cooldown');
            if (!$stack->offCooldown) {
                throw new \Exception('This companion is on transfer cooldown! Companions have a '.$cooldown.' day cooldown period between user transfers.');
            }
            
            $stack->user_id = $recipient->id;
            $stack->save();

            if ($type && !$this->createLog($sender ? $sender->id : null, $recipient->id, $stack->id, $type, $data['data'], $stack->pet_id, 1)) {
                throw new \Exception('Failed to create log.');
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Debits an pet from a user.
     *
     * @param User                    $user
     * @param string                  $type
     * @param array                   $data
     * @param \App\Models\Pet\UserPet $stack
     *
     * @return bool
     */
    public function debitStack($user, $type, $data, $stack) {
        DB::beginTransaction();

        try {
            $stack->delete();

            if ($type && !$this->createLog($user ? $user->id : null, null, $stack->id, $type, $data['data'], $stack->pet_id, 1)) {
                throw new \Exception('Failed to create log.');
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Adjusts bonding value (experience points) for a pet (staff only).
     *
     * @param mixed $pet
     * @param int   $amount
     * @param mixed $staff
     */
    public function adjustBonding($pet, $amount, $staff) {
        DB::beginTransaction();

        try {
            if (!$staff->hasPower('edit_inventories')) {
                throw new \Exception('You do not have permission to adjust pet experience.');
            }
            if (!$pet) {
                throw new \Exception('An invalid companion was selected.');
            }
            if (!$amount) {
                throw new \Exception('Invalid value for experience inputted.');
            }

            $pet->ensureLevel();

            $oldBonding = $pet->level->bonding;
            // prevent exp from going below 0
            $newBonding = max(0, $oldBonding + $amount);
            $pet->level->bonding = $newBonding;
            $pet->level->save();

            $delta = $newBonding - $oldBonding;
            $logType = 'Pet EXP Edit';
            $logData = '[Staff] Adjusted the experience value of '.$pet->fullName.' ('.($delta >= 0 ? '+' : '').$delta.' EXP, now at '.$pet->level->bonding.' EXP)';

            if (!$this->createLog($staff->id, $pet->user->id ?? null, $pet->id, $logType, $logData, $pet->pet->id ?? null, 1)) {
                throw new \Exception('Failed to create pet experience edit log.');
            }

            $this->processLevelChange($pet);

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Processes any level-ups for a pet based on its current bonding and time.
     *
     * @param mixed $pet
     */
    public function processLevelChange($pet) {
        if (!$pet->level) {
            return;
        }
        $maxLevel = Settings::get('max_pet_level');
        $today = Carbon::now();

        while (($pet->level->levelsAt < $today) && (!$maxLevel || $pet->level->bonding_level < $maxLevel)) {
            $daysUntilLevel = Carbon::now()->diffInDays($pet->level->nextLevel, false);
            $weeksConsumed = $daysUntilLevel > 0 ? (int) ceil($daysUntilLevel / 7) : 0;

            $pet->level->bonding_level++;
            $pet->level->bonding -= $weeksConsumed;
            $pet->level->next_level_at = Carbon::now()->addYear()->startOfDay();
            $pet->level->save();

            $this->logLevelChange($pet, 'Level Up', 'levelled up');
        }
    }

    /**
     * Creates an inventory log.
     *
     * @param int    $senderId
     * @param int    $recipientId
     * @param int    $stackId
     * @param string $type
     * @param string $data
     * @param int    $quantity
     * @param mixed  $petId
     *
     * @return int
     */
    public function createLog($senderId, $recipientId, $stackId, $type, $data, $petId, $quantity) {
        return DB::table('user_pets_log')->insert(
            [
                'sender_id'    => $senderId,
                'recipient_id' => $recipientId,
                'stack_id'     => $stackId,
                'log'          => $type.($data ? ' ('.$data.')' : ''),
                'log_type'     => $type,
                'data'         => $data, // this should be just a string
                'pet_id'       => $petId,
                'quantity'     => $quantity,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now(),
            ]
        );
    }

    /**
     * Writes a level up/down log entry for a pet.
     *
     * @param mixed $pet
     */
    private function logLevelChange($pet, string $logType, string $verb): void {
        $logData = 'Pet '.$pet->fullName.' '.$verb.'. It is now level '.$pet->level->bonding_level;
        $this->createLog($pet->user_id ?? null, $pet->user_id ?? null, $pet->id, $logType, $logData, $pet->pet_id ?? null, 1);
    }

    /**
     * Checks if a pet is on attach/detach cooldown.
     *
     * @param mixed $pet
     * @param mixed $user
     */
    private function checkCooldown($pet, $user) {
        $cooldownDays = Settings::get('claymore_cooldown');
        if ($cooldownDays && $pet->attached_at && !$user->hasPower('edit_inventories')) {
            $cooldownExpires = Carbon::parse($pet->attached_at)->addDays($cooldownDays);
            if ($cooldownExpires->isFuture()) {
                throw new \Exception('This companion is on cooldown until '.$cooldownExpires->format('M j, Y H:i').'.');
            }
        }
    }
}
