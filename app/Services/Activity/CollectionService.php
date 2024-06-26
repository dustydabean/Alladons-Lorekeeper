<?php

namespace App\Services\Activity;

use App\Models\Collection\Collection;
use App\Services\CollectionService as CollectionManager;
use App\Services\Service;

use DB;

class CollectionService extends Service {

  /**
   * Retrieves any data that should be used in the activity module editing form on the admin side
   *
   * @return array
   */
  public function getEditData() {

    return [
      'collections' => Collection::orderBy('name', 'ASC')->pluck('name', 'id'),
    ];
  }

  /**
   * Retrieves any data that should be used in the activity module on the user side
   *
   * @return array
   */
  public function getActData($activity) {
    
    return [
      'collection' => Collection::find($activity->data)
    ];
  }

  /**
   * Processes the data attribute of the activity and returns it in the preferred format.
   *
   * @param  string  $tag
   * @return mixed
   */
  public function getData($data) {
    return $data;
  }

  /**
   * Processes the data attribute of the activity and returns it in the preferred format.
   *
   * @param  object  $activity
   * @param  array   $data
   * @return bool
   */
  public function updateData($activity, $data) {
    return $data['collection_id'];
  }

  /**
   * Acts upon the item when used from the inventory.
   *
   * @param  \App\Models\User\UserItem  $stacks
   * @param  \App\Models\User\User      $user
   * @param  array                      $data
   * @return bool
   */
  public function act($activity, $data, $user) {
    DB::beginTransaction();

    try {
      $collection = Collection::find($activity->data);
      $isComplete = $this->checkCollection($user, $collection);

      if (!$isComplete) throw new \Exception('You haven\'t completed this Collection.');

      // Credit rewards
      $logType = 'Collection Reward';
      $rewardData = [
        'data' => 'Received rewards from ' . $collection->displayName . ' collection'
      ];

      if (!$rewards = fillUserAssets($collection->rewardItems, null, $user, $logType, $rewardData)) throw new \Exception("Failed to distribute rewards to user.");

      // Complete the Collection
      $collectionData = [
        'data' => 'Completed ' . $collection->displayName . ' collection'
      ];
      if (!(new CollectionManager)->creditCollection(null, $user, null, 'Collection Completed', $collectionData, $collection)) throw new \Exception('Failed to create collection log.');

      flash(getRewardsString($rewards));

      return $this->commitReturn(true);
    } catch (\Exception $e) {
      $this->setError('error', $e->getMessage());
    }
    return $this->rollbackReturn(false);
  }

  public function checkCollection($user, $collection) {
    $completed = true;
    foreach ($collection->ingredients as $ingredient) {
      $userOwned = \App\Models\User\UserItem::where('user_id', $user->id)->where('item_id', $ingredient->ingredient->id)->where('count', '>', 0)->get();
      if (!$userOwned->count()) $completed = false;
    }

    return $completed;
  }
}
