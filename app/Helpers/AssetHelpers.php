<?php

/*
|--------------------------------------------------------------------------
| Asset Helpers
|--------------------------------------------------------------------------
|
| These are used to manage asset arrays, which are used in keeping
| track of/distributing rewards.
|
*/

/**
 * Gets the asset keys for an array depending on whether the
 * assets being managed are owned by a user or character.
 *
 * @param bool $isCharacter
 *
 * @return array
 */
function getAssetKeys($isCharacter = false) {
    if (!$isCharacter) {
        return ['items', 'currencies', 'pets', 'pet_variants', 'raffle_tickets', 'loot_tables', 'user_items', 'characters', 'recipes', 'themes'];
    } else {
        return ['currencies', 'items', 'character_items', 'loot_tables'];
    }
}

/**
 * Gets the model name for an asset type.
 * The asset type has to correspond to one of the asset keys above.
 *
 * @param string $type
 * @param bool   $namespaced
 *
 * @return string
 */
function getAssetModelString($type, $namespaced = true) {
    switch ($type) {
        case 'items': case 'item':
            if ($namespaced) {
                return '\App\Models\Item\Item';
            } else {
                return 'Item';
            }
            break;

        case 'currencies': case 'currency':
            if ($namespaced) {
                return '\App\Models\Currency\Currency';
            } else {
                return 'Currency';
            }
            break;

        case 'pets': case 'pet':
            if ($namespaced) {
                return '\App\Models\Pet\Pet';
            } else {
                return 'Pet';
            }
            break;
        case 'pet_variants': case 'pet_variant':
            if ($namespaced) {
                return '\App\Models\Pet\PetVariant';
            } else {
                return 'PetVariant';
            }
            break;
        case 'raffle_tickets':
            if ($namespaced) {
                return '\App\Models\Raffle\Raffle';
            } else {
                return 'Raffle';
            }
            break;

        case 'loot_tables':
            if ($namespaced) {
                return '\App\Models\Loot\LootTable';
            } else {
                return 'LootTable';
            }
            break;

        case 'user_items':
            if ($namespaced) {
                return '\App\Models\User\UserItem';
            } else {
                return 'UserItem';
            }
            break;

        case 'characters':
            if ($namespaced) {
                return '\App\Models\Character\Character';
            } else {
                return 'Character';
            }
            break;

        case 'recipes':
            if ($namespaced) {
                return '\App\Models\Recipe\Recipe';
            } else {
                return 'Recipe';
            }
            break;

        case 'character_items':
            if ($namespaced) {
                return '\App\Models\Character\CharacterItem';
            } else {
                return 'CharacterItem';
            }
            break;

        case 'themes':
            if ($namespaced) {
                return '\App\Models\Theme';
            } else {
                return 'Theme';
            }
            break;
    }

    return null;
}

/**
 * Initialises a new blank assets array, keyed by the asset type.
 *
 * @param bool $isCharacter
 *
 * @return array
 */
function createAssetsArray($isCharacter = false) {
    $keys = getAssetKeys($isCharacter);
    $assets = [];
    foreach ($keys as $key) {
        $assets[$key] = [];
    }

    return $assets;
}

/**
 * Merges 2 asset arrays.
 *
 * @param array $first
 * @param array $second
 *
 * @return array
 */
function mergeAssetsArrays($first, $second) {
    $keys = getAssetKeys();
    foreach ($keys as $key) {
        if (isset($second[$key])) {
            foreach ($second[$key] as $item) {
                addAsset($first, $item['asset'], $item['quantity']);
            }
        }
    }

    return $first;
}

/**
 * Adds an asset to the given array.
 * If the asset already exists, it adds to the quantity.
 *
 * @param array $array
 * @param mixed $asset
 * @param int   $quantity
 */
function addAsset(&$array, $asset, $quantity = 1) {
    if (!$asset) {
        return;
    }
    if (isset($array[$asset->assetType][$asset->id])) {
        $array[$asset->assetType][$asset->id]['quantity'] += $quantity;
    } else {
        $array[$asset->assetType][$asset->id] = ['asset' => $asset, 'quantity' => $quantity];
    }
}

/**
 * Removes an asset from the given array, if it exists.
 *
 * @param array $array
 * @param mixed $asset
 * @param int   $quantity
 */
function removeAsset(&$array, $asset, $quantity = 1) {
    if (!$asset) {
        return;
    }
    if (isset($array[$asset->assetType][$asset->id])) {
        $array[$asset->assetType][$asset->id]['quantity'] -= $quantity;
        if ($array[$asset->assetType][$asset->id]['quantity'] == 0) {
            unset($array[$asset->assetType][$asset->id]);
        }
    }
}

/**
 * Get a clean version of the asset array to store in the database,
 * where each asset is listed in [id => quantity] format.
 *
 * @param array $array
 * @param bool  $isCharacter
 *
 * @return array
 */
function getDataReadyAssets($array, $isCharacter = false) {
    $result = [];
    foreach ($array as $key => $type) {
        if ($type && !isset($result[$key])) {
            $result[$key] = [];
        }
        foreach ($type as $assetId => $assetData) {
            $result[$key][$assetId] = $assetData['quantity'];
        }
    }

    return $result;
}

// --------------------------------------------

/**
 * Adds an asset to the given array.
 * If the asset already exists, it adds to the quantity.
 *
 * @param array $array
 * @param mixed $asset
 * @param mixed $min_quantity
 * @param mixed $max_quantity
 */
function addDropAsset(&$array, $asset, $min_quantity = 1, $max_quantity = 1) {
    if (!$asset) {
        return;
    }
    if (isset($array[$asset->assetType][$asset->id])) {
        return;
    } else {
        $array[$asset->assetType][$asset->id] = ['asset' => $asset, 'min_quantity' => $min_quantity, 'max_quantity' => $max_quantity];
    }
}

/**
 * Get a clean version of the asset array to store in the database,
 * where each asset is listed in [id => quantity] format.
 * json_encode this and store in the data attribute.
 *
 * @param array $array
 *
 * @return array
 */
function getDataReadyDropAssets($array) {
    $result = [];
    foreach ($array as $group => $types) {
        $result[$group] = [];
        foreach ($types as $type => $key) {
            if ($type && !isset($result[$group][$type])) {
                $result[$group][$type] = [];
            }
            foreach ($key as $assetId => $assetData) {
                $result[$group][$type][$assetId] = [
                    'min_quantity' => $assetData['min_quantity'],
                    'max_quantity' => $assetData['max_quantity'],
                ];
            }
            if (empty($result[$group][$type])) {
                unset($result[$group][$type]);
            }
        }
    }

    return $result;
}

/**
 * Retrieves the data associated with an asset array,
 * basically reversing the above function.
 * Use the data attribute after json_decode()ing it.
 *
 * @param array $array
 *
 * @return array
 */
function parseDropAssetData($array) {
    $result = [];
    foreach ($array as $group => $types) {
        $result[$group] = [];
        foreach ($types as $type => $contents) {
            $model = getAssetModelString($type);
            if ($model) {
                foreach ($contents as $id => $data) {
                    $result[$group][$type][$id] = [
                        'asset'        => $model::find($id),
                        'min_quantity' => $data['min_quantity'],
                        'max_quantity' => $data['max_quantity'],
                    ];
                }
            }
        }
    }

    return $result;
}

// --------------------------------------------

/**
 * Retrieves the data associated with an asset array,
 * basically reversing the above function.
 *
 * @param array $array
 *
 * @return array
 */
function parseAssetData($array) {
    $assets = createAssetsArray();
    foreach ($array as $key => $contents) {
        $model = getAssetModelString($key);
        if ($model) {
            foreach ($contents as $id => $quantity) {
                $assets[$key][$id] = [
                    'asset'    => $model::find($id),
                    'quantity' => $quantity,
                ];
            }
        }
    }

    return $assets;
}

/**
 * Returns if two asset arrays are identical.
 *
 * @param array $first
 * @param array $second
 * @param mixed $isCharacter
 * @param mixed $absQuantities
 *
 * @return bool
 */
function compareAssetArrays($first, $second, $isCharacter = false, $absQuantities = false) {
    $keys = getAssetKeys($isCharacter);
    foreach ($keys as $key) {
        if (count($first[$key]) != count($second[$key])) {
            return false;
        }
        foreach ($first[$key] as $id => $asset) {
            if (!isset($second[$key][$id])) {
                return false;
            }

            if ($absQuantities) {
                if (abs($asset['quantity']) != abs($second[$key][$id]['quantity'])) {
                    return false;
                }
            } else {
                if ($asset['quantity'] != $second[$key][$id]['quantity']) {
                    return false;
                }
            }
        }
    }

    return true;
}

/**
 * Distributes the assets in an assets array to the given recipient (user).
 * Loot tables will be rolled before distribution.
 *
 * @param array                $assets
 * @param App\Models\User\User $sender
 * @param App\Models\User\User $recipient
 * @param string               $logType
 * @param string               $data
 * @param mixed|null           $selected
 *
 * @return array
 */
function fillUserAssets($assets, $sender, $recipient, $logType, $data, $selected = null) {
    // Roll on any loot tables
    if (isset($assets['loot_tables'])) {
        foreach ($assets['loot_tables'] as $table) {
            $assets = mergeAssetsArrays($assets, $table['asset']->roll($table['quantity']));
        }
        unset($assets['loot_tables']);
    }

    foreach ($assets as $key => $contents) {
        if ($key == 'items' && count($contents)) {
            $service = new App\Services\InventoryManager;
            foreach ($contents as $asset) {
                if ($asset['quantity'] < 0) {
                    if (!$selected) {
                        flash('No selected item found for debiting.')->error();

                        return false;
                    }

                    foreach ($selected as $stackData) {
                        if (!$service->debitStack($sender, $logType, $data, $stackData['stack'], $stackData['quantity'])) {
                            foreach ($service->errors()->getMessages()['error'] as $error) {
                                flash($error)->error();
                            }

                            return false;
                        }
                    }
                } else {
                    if (!$service->creditItem($sender, $recipient, $logType, $data, $asset['asset'], $asset['quantity'])) {
                        foreach ($service->errors()->getMessages()['error'] as $error) {
                            flash($error)->error();
                        }

                        return false;
                    }
                }
            }
        } elseif ($key == 'currencies' && count($contents)) {
            $service = new App\Services\CurrencyManager;
            foreach ($contents as $asset) {
                if ($asset['quantity'] < 0) {
                    if (!$service->debitCurrency($sender, $recipient, $logType, $data['data'], $asset['asset'], abs($asset['quantity']))) {
                        foreach ($service->errors()->getMessages()['error'] as $error) {
                            flash($error)->error();
                        }

                        return false;
                    }
                } else {
                    if (!$service->creditCurrency($sender, $recipient, $logType, $data['data'], $asset['asset'], $asset['quantity'])) {
                        foreach ($service->errors()->getMessages()['error'] as $error) {
                            flash($error)->error();
                        }

                        return false;
                    }
                }
            }
        } elseif ($key == 'pets' && count($contents)) {
            $service = new App\Services\PetManager;
            foreach ($contents as $asset) {
                if (!$service->creditPet($sender, $recipient, $logType, $data, $asset['asset'], $asset['quantity'])) {
                    return false;
                }
            }
        } elseif ($key == 'pet_variants' && count($contents)) {
            $service = new App\Services\PetManager;
            foreach ($contents as $asset) {
                if (!$service->creditPet($sender, $recipient, $logType, $data, $asset['asset']->pet, $asset['quantity'], $asset['asset']->id)) {
                    return false;
                }
            }
        } elseif ($key == 'raffle_tickets' && count($contents)) {
            $service = new App\Services\RaffleManager;
            foreach ($contents as $asset) {
                if (!$service->addTicket($recipient, $asset['asset'], $asset['quantity'])) {
                    foreach ($service->errors()->getMessages()['error'] as $error) {
                        flash($error)->error();
                    }

                    return false;
                }
            }
        } elseif ($key == 'user_items' && count($contents)) {
            $service = new App\Services\InventoryManager;
            foreach ($contents as $asset) {
                if (!$service->moveStack($sender, $recipient, $logType, $data, $asset['asset'], $asset['quantity'])) {
                    foreach ($service->errors()->getMessages()['error'] as $error) {
                        flash($error)->error();
                    }

                    return false;
                }
            }
        } elseif ($key == 'characters' && count($contents)) {
            $service = new App\Services\CharacterManager;
            foreach ($contents as $asset) {
                if (!$service->moveCharacter($asset['asset'], $recipient, $data, $asset['quantity'], $logType)) {
                    foreach ($service->errors()->getMessages()['error'] as $error) {
                        flash($error)->error();
                    }

                    return false;
                }
            }
        } elseif ($key == 'themes' && count($contents)) {
            $service = new App\Services\ThemeManager;
            foreach ($contents as $asset) {
                if (!$service->creditTheme($recipient, $asset['asset'])) {
                    return false;
                }
            }
        } elseif ($key == 'recipes' && count($contents)) {
            $service = new App\Services\RecipeService;
            foreach ($contents as $asset) {
                if (!$service->creditRecipe($sender, $recipient, null, $logType, $data, $asset['asset'])) {
                    return false;
                }
            }
        }
    }

    return $assets;
}

/**
 * Returns the total count of all assets in an asset array.
 *
 * @param array $array
 *
 * @return int
 */
function countAssets($array) {
    $count = 0;
    foreach ($array as $key => $contents) {
        foreach ($contents as $asset) {
            $count += $asset['quantity'];
        }
    }

    return $count;
}

/**
 * Distributes the assets in an assets array to the given recipient (character).
 * Loot tables will be rolled before distribution.
 *
 * @param array                          $assets
 * @param App\Models\User\User           $sender
 * @param App\Models\Character\Character $recipient
 * @param string                         $logType
 * @param string                         $data
 * @param mixed|null                     $submitter
 *
 * @return array
 */
function fillCharacterAssets($assets, $sender, $recipient, $logType, $data, $submitter = null) {
    if (!config('lorekeeper.extensions.character_reward_expansion.default_recipient') && $recipient->user) {
        $item_recipient = $recipient->user;
    } else {
        $item_recipient = $submitter;
    }

    // Roll on any loot tables
    if (isset($assets['loot_tables'])) {
        foreach ($assets['loot_tables'] as $table) {
            $assets = mergeAssetsArrays($assets, $table['asset']->roll($table['quantity']));
        }
        unset($assets['loot_tables']);
    }

    foreach ($assets as $key => $contents) {
        if ($key == 'currencies' && count($contents)) {
            $service = new App\Services\CurrencyManager;
            foreach ($contents as $asset) {
                if (!$service->creditCurrency($sender, ($asset['asset']->is_character_owned ? $recipient : $item_recipient), $logType, $data['data'], $asset['asset'], $asset['quantity'])) {
                    return false;
                }
            }
        } elseif ($key == 'items' && count($contents)) {
            $service = new App\Services\InventoryManager;
            foreach ($contents as $asset) {
                if (!$service->creditItem($sender, (($asset['asset']->category && $asset['asset']->category->is_character_owned) ? $recipient : $item_recipient), $logType, $data, $asset['asset'], $asset['quantity'])) {
                    return false;
                }
            }
        }
    }

    return $assets;
}

/**
 * Rolls on a loot-table esque rewards setup.
 */
function rollRewards($loot, $quantity = 1)
{
    $rewards = createAssetsArray();

    $totalWeight = 0;
    foreach($loot as $l) $totalWeight += $l->weight;

    for($i = 0; $i < $quantity; $i++)
    {
        $roll = mt_rand(0, $totalWeight - 1);
        $result = null;
        $prev = null;
        $count = 0;
        foreach($loot as $l)
        {
            $count += $l->weight;

            if($roll < $count)
            {
                $result = $l;
                break;
            }
            $prev = $l;
        }
        if(!$result) $result = $prev;

        if($result) {
            // If this is chained to another loot table, roll on that table
            if($result->rewardable_type == 'LootTable') $rewards = mergeAssetsArrays($rewards, $result->reward->roll($result->quantity));
            elseif($result->rewardable_type == 'ItemCategory' || $result->rewardable_type == 'ItemCategoryRarity') $rewards = mergeAssetsArrays($rewards, rollCategory($result->rewardable_id, $result->quantity, (isset($result->data['criteria']) ? $result->data['criteria'] : null), (isset($result->data['rarity']) ? $result->data['rarity'] : null)));
            elseif($result->rewardable_type == 'ItemRarity') $rewards = mergeAssetsArrays($rewards, rollRarityItem($result->quantity, $result->data['criteria'], $result->data['rarity']));
            else addAsset($rewards, $result->reward, $result->quantity);
        }
    }
    return $rewards;
}

/**
 * Rolls on an item category.
 *
 * @param  int    $id
 * @param  int    $quantity
 * @param  string $condition
 * @param  string $rarity
 * @return \Illuminate\Support\Collection
 */
function rollCategory($id, $quantity = 1, $criteria = null, $rarity = null)
{
    $rewards = createAssetsArray();

    if(isset($criteria) && $criteria && isset($rarity) && $rarity) {
        if(config('lorekeeper.extensions.item_entry_expansion.loot_tables.alternate_filtering')) $loot = Item::where('item_category_id', $id)->released()->whereNotNull('data')->where('data->rarity', $criteria, $rarity)->get();
        else $loot = Item::where('item_category_id', $id)->released()->whereNotNull('data')->whereRaw('JSON_EXTRACT(`data`, \'$.rarity\')'. $criteria . $rarity)->get();
    }
    else $loot = Item::where('item_category_id', $id)->released()->get();
    if(!$loot->count()) throw new \Exception('There are no items to select from!');

    $totalWeight = $loot->count();

    for($i = 0; $i < $quantity; $i++)
    {
        $roll = mt_rand(0, $totalWeight - 1);
        $result = $loot[$roll];

        if($result) {
            // If this is chained to another loot table, roll on that table
            addAsset($rewards, $result, 1);
        }
    }
    return $rewards;
}

/**
 * Rolls on an item rarity.
 *
 * @param  int    $quantity
 * @param  string $condition
 * @param  string $rarity
 * @return \Illuminate\Support\Collection
 */
function rollRarityItem($quantity = 1, $criteria, $rarity)
{
    $rewards = createAssetsArray();

    if(config('lorekeeper.extensions.item_entry_expansion.loot_tables.alternate_filtering')) $loot = Item::released()->whereNotNull('data')->where('data->rarity', $criteria, $rarity)->get();
    else $loot = Item::released()->whereNotNull('data')->whereRaw('JSON_EXTRACT(`data`, \'$.rarity\')'. $criteria . $rarity)->get();
    if(!$loot->count()) throw new \Exception('There are no items to select from!');

    $totalWeight = $loot->count();

    for($i = 0; $i < $quantity; $i++)
    {
        $roll = mt_rand(0, $totalWeight - 1);
        $result = $loot[$roll];

        if($result) {
            // If this is chained to another loot table, roll on that table
            addAsset($rewards, $result, 1);
        }
    }
    return $rewards;
}

/**
 * Creates a rewards string from an asset array.
 *
 * @param array $array
 * @param mixed $useDisplayName
 * @param mixed $absQuantities
 *
 * @return string
 */
function createRewardsString($array, $useDisplayName = true, $absQuantities = false) {
    $string = [];
    foreach ($array as $key => $contents) {
        foreach ($contents as $asset) {
            if ($useDisplayName) {
                if ($key == 'currencies') {
                    $name = $asset['asset'] ? $asset['asset']->display(($absQuantities ? abs($asset['quantity']) : $asset['quantity'])) : 'Deleted '.ucfirst(str_replace('_', ' ', $key));
                    $string[] = $asset['asset'] ? $name : $name.' x'.($absQuantities ? abs($asset['quantity']) : $asset['quantity']);
                } else {
                    $name = $asset['asset']->displayName ?? ($asset['asset']->name ?? 'Deleted '.ucfirst(str_replace('_', ' ', $key)));
                    $string[] = $name.' x'.($absQuantities ? abs($asset['quantity']) : $asset['quantity']);
                }
            } else {
                $name = $asset['asset']->name ?? 'Deleted '.ucfirst(str_replace('_', ' ', $key));
                $string[] = $name.' x'.($absQuantities ? abs($asset['quantity']) : $asset['quantity']);
            }
        }
    }
    if (!count($string)) {
        return 'Nothing. :('; // :(
    }

    if (count($string) == 1) {
        return implode(', ', $string);
    }

    return implode(', ', array_slice($string, 0, count($string) - 1)).(count($string) > 2 ? ', and ' : ' and ').end($string);
}

/**
 * Returns an asset from provided data.
 *
 * @param mixed $type
 * @param mixed $id
 * @param mixed $isCharacter
 */
function findReward($type, $id, $isCharacter = false) {
    $reward = null;
    switch ($type) {
        case 'Item':
            $reward = App\Models\Item\Item::find($id);
            break;
        case 'Currency':
            $reward = App\Models\Currency\Currency::find($id);
            if (!$isCharacter && !$reward->is_user_owned) {
                throw new Exception('Invalid currency selected.');
            }
            break;
        case 'Pet':
            $reward = App\Models\Pet\Pet::find($id);
            break;
        case 'LootTable':
            $reward = App\Models\Loot\LootTable::find($id);
            break;
        case 'Raffle':
            $reward = App\Models\Raffle\Raffle::find($id);
            break;
    }

    return $reward;
}

/** Rewards list for user notification.
 *
 * @param array $rewards
 *
 * @return string
 */
function getRewardsString($rewards) {
    $results = 'You have received: ';
    $result_elements = [];
    foreach ($rewards as $assetType) {
        if (isset($assetType)) {
            foreach ($assetType as $asset) {
                array_push($result_elements, $asset['asset']->name.(class_basename($asset['asset']) == 'Raffle' ? ' (Raffle Ticket)' : '').' x'.$asset['quantity']);
            }
        }
    }

    return $results.implode(', ', $result_elements);
}
