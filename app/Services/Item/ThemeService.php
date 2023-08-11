<?php

namespace App\Services\Item;

use App\Models\Theme;
use App\Services\Service;

use DB;

use App\Services\InventoryManager;

class ThemeService extends Service {
  /*
    |--------------------------------------------------------------------------
    | Theme Service
    |--------------------------------------------------------------------------
    |
    | Handles the editing and usage of theme type items.
    |
    */

  /**
   * Retrieves any data that should be used in the item tag editing form.
   *
   * @return array
   */
  public function getEditData() {
    return [
      'themes' => Theme::orderBy('name')->where('is_user_selectable', 0)->pluck('name', 'id'),
    ];
  }

  /**
   * Processes the data attribute of the tag and returns it in the preferred format.
   *
   * @param  object  $tag
   * @return mixed
   */
  public function getTagData($tag) {
    if (isset($tag->data['all_themes'])) return 'All';
    $rewards = [];
    if ($tag->data) {
      $assets = parseAssetData($tag->data);
      foreach ($assets as $type => $a) {
        $class = getAssetModelString($type, false);
        foreach ($a as $id => $asset) {
          $rewards[] = (object)[
            'rewardable_type' => $class,
            'rewardable_id' => $id,
            'quantity' => 1
          ];
        }
      }
    }
    return $rewards;
  }

  /**
   * Processes the data attribute of the tag and returns it in the preferred format.
   *
   * @param  object  $tag
   * @param  array   $data
   * @return bool
   */
  public function updateData($tag, $data) {
    DB::beginTransaction();

    try {
      // If there's no data, return.
      if (!isset($data['rewardable_id']) && !isset($data['all_themes'])) return true;
      if (isset($data['all_themes'])) $assets = ['all_themes' => 1];
      else {
        // The data will be stored as an asset table, json_encode()d.
        // First build the asset table, then prepare it for storage.
        $assets = createAssetsArray();
        foreach ($data['rewardable_id'] as $key => $r) {
          $asset = Theme::find($data['rewardable_id'][$key]);
          addAsset($assets, $asset, 1);
        }
        $assets = getDataReadyAssets($assets);
      }

      $tag->update(['data' => json_encode($assets)]);

      return $this->commitReturn(true);
    } catch (\Exception $e) {
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
  public function act($stacks, $user, $data) {
    DB::beginTransaction();

    try {
      $firstData = $stacks->first()->item->tag('theme')->data;
      if (isset($firstData['all_themes']) && $firstData['all_themes']) {
        $themeOptions = Theme::where('is_user_selectable', 0)->whereNotIn('id', $user->themes->pluck('id')->toArray())->get();
      } elseif (isset($firstData['themes']) && count($firstData['themes'])) {
        $themeOptions = Theme::find(array_keys($firstData['themes']))->where('is_user_selectable', 0)->whereNotIn('id', $user->themes->pluck('id')->toArray());
      }

      $options = $themeOptions->pluck('id')->toArray();
      if (!count($options)) throw new \Exception("There are no more options for this theme redemption item.");
      if (count($options) < array_sum($data['quantities'])) throw new \Exception("You have selected a quantity too high for the quantity of themes you can unlock with this item.");

      foreach ($stacks as $key => $stack) {

        // We don't want to let anyone who isn't the owner of the box open it,
        // so do some validation...
        if ($stack->user_id != $user->id) throw new \Exception("This item does not belong to you.");

        // Next, try to delete the box item. If successful, we can start distributing rewards.
        if ((new InventoryManager)->debitStack($stack->user, 'Theme Redeemed', ['data' => ''], $stack, $data['quantities'][$key])) {
          for ($q = 0; $q < $data['quantities'][$key]; $q++) {

            $random = array_rand($options);
            $thisTheme['themes'] = [$options[$random] => 1];
            unset($options[$random]);

            // Distribute user rewards
            if (!$rewards = fillUserAssets(parseAssetData($thisTheme), $user, $user, 'Theme Redemption', [
              'data' => 'Redeemed from ' . $stack->item->name
            ])) throw new \Exception("Failed to open theme redemption item.");
            flash($this->getThemeRewardsString($rewards));
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
   * Acts upon the item when used from the inventory.
   *
   * @param  array                  $rewards
   * @return string
   */
  private function getThemeRewardsString($rewards) {
    $results = "You have unlocked the following theme: ";
    $result_elements = [];
    foreach ($rewards as $assetType) {
      if (isset($assetType)) {
        foreach ($assetType as $asset) {
          array_push($result_elements, $asset['asset']->displayName);
        }
      }
    }
    return $results . implode(', ', $result_elements);
  }
}
