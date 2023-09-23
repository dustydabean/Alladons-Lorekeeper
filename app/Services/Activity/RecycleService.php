<?php

namespace App\Services\Activity;

use App\Models\Currency\Currency;
use App\Models\Item\Item;
use App\Models\Item\ItemCategory;
use App\Models\Loot\LootTable;
use App\Models\Raffle\Raffle;
use App\Models\User\UserItem;
use App\Services\InventoryManager;
use App\Services\Service;

use DB;
use Auth;

class RecycleService extends Service {

  /**
   * Retrieves any data that should be used in the activity module editing form on the admin side
   *
   * @return array
   */
  public function getEditData() {
    $rarities = Item::whereNotNull('data')->get()->pluck('rarity')->unique()->toArray();
    sort($rarities);

    return [
      'items' => Item::orderBy('name')->pluck('name', 'id'),
      'rarities' => array_filter($rarities),
      'categories' => ItemCategory::orderBy('sort', 'DESC')->pluck('name', 'id'),
      'characterCurrencies' => Currency::where('is_character_owned', 1)->orderBy('sort_character', 'DESC')->pluck('name', 'id'),
      'currencies' => Currency::where('is_user_owned', 1)->orderBy('name')->pluck('name', 'id'),
      'tables' => LootTable::orderBy('name')->pluck('name', 'id'),
      'raffles' => Raffle::where('rolled_at', null)->where('is_active', 1)->orderBy('name')->pluck('name', 'id'),
    ];
  }

  /**
   * Retrieves any data that should be used in the activity module on the user side
   *
   * @return array
   */
  public function getActData($activity) {
    $data = $activity->data;
    $inventory = UserItem::with('item')->whereNull('deleted_at')->where('count', '>', '0')->where('user_id', Auth::user()->id);
    $recyclables = collect($data->recyclables);

    // Filter inventory by valid recyclables
    $inventory = $inventory->where(function ($query) use ($recyclables) {
      $itemIds = $recyclables->where('rewardable_type', 'Item')->pluck('rewardable_id');
      $itemCategoryIds = $recyclables->where('rewardable_type', 'ItemCategory')->pluck('rewardable_id');
      $query->whereIn('item_id', $itemIds)->orWhereHas('item', function ($query) use ($itemCategoryIds) {
        $query->whereIn('item_category_id', $itemCategoryIds);
      });
    });

    $inventory = $inventory->get()
      ->sortBy('item.name');
    return [
      'inventory' => $inventory,
      'categories' => ItemCategory::orderBy('sort', 'DESC')->get(),
      'item_filter' => Item::orderBy('name')->get()->keyBy('id'),
    ];
  }

  /**
   * Processes the data attribute of the activity and returns it in the preferred format.
   *
   * @param  string  $tag
   * @return mixed
   */
  public function getData($data) {

    $rewards = [];
    $dataObj = json_decode($data);
    if ($dataObj && $dataObj->loot) {
      $assets = parseAssetData($dataObj->loot);
      foreach ($assets as $type => $a) {
        $class = getAssetModelString($type, false);
        foreach ($a as $id => $asset) {
          $rewards[] = (object)[
            'rewardable_type' => $class,
            'rewardable_id' => $id,
            'quantity' => $asset['quantity']
          ];
        }
      }
    }

    if ($dataObj) {
      $dataObj->lootPlain = $dataObj->loot;
      $dataObj->loot = $rewards;
    }

    return $dataObj;
  }

  /**
   * Processes the data attribute of the activity and returns it in the preferred format.
   *
   * @param  object  $activity
   * @param  array   $data
   * @return bool
   */
  public function updateData($activity, $data) {
    if (isset($data['recyclable_type'])) {
      $recyclables = [];
      foreach ($data['recyclable_type'] as $key => $r) {
        $recyclables[] = (object) [
          'rewardable_type' => $data['recyclable_type'][$key],
          'rewardable_id' => $data['recyclable_id'][$key],
        ];
      }
    }

    if (isset($data['rewardable_type'])) {
      $assets = createAssetsArray();
      foreach ($data['rewardable_type'] as $key => $r) {
        switch ($r) {
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
        addAsset($assets, $asset, $data['quantity'][$key]);
      }
      $assets = getDataReadyAssets($assets);
    }

    return json_encode([
      'loot' => $assets ?? null,
      'recyclables' => $recyclables ?? null,
      'quantity' => $data['recyclableQuantity']
    ]);
  }

  /**
   * Code executed by the act post url being hit (expected from the user-side blade file at resources/views/activities/modules/recycle.blade.php)
   *
   * @param  \App\Models\User\UserItem  $stacks
   * @param  \App\Models\User\User      $user
   * @param  array                      $data
   * @return bool
   */
  public function act($activity, $data, $user) {
    DB::beginTransaction();

    try {
      if (!isset($data['stack_quantity'])) throw new \Exception('Please select the items to turn in.');
      if (array_sum($data['stack_quantity']) % $activity->data->quantity !== 0) throw new \Exception('Please select the correct number of items to turn in. It should be divisible by ' . $activity->data->quantity);

      foreach ($data['stack_id'] as $stackId) {
        $stack = UserItem::find($stackId);
        if (!(new InventoryManager)->debitStack($stack->user, 'Turned in to' . $activity->name, ['data' => ''], $stack, $data['stack_quantity'][$stackId]))
          throw new \Exception('Failed to remove item');
      }

      $rewardCount =  array_sum($data['stack_quantity']) / $activity->data->quantity;
      for ($i = 0; $i < $rewardCount; $i++) {
        if (!$rewards = fillUserAssets(parseAssetData($activity->data->lootPlain), $user, $user, $activity->name . ' Rewards', [
          'data' => 'Received rewards from turning items into ' . $activity->name
        ])) throw new \Exception("Failed to process rewards.");
        if (!isset($allRewards)) $allRewards = $rewards;
        else $allRewards = mergeAssetsArrays($allRewards, $rewards);
      }
      flash(getRewardsString($allRewards));

      return $this->commitReturn(true);
    } catch (\Exception $e) {
      $this->setError('error', $e->getMessage());
    }
    return $this->rollbackReturn(false);
  }
}
