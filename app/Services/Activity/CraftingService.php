<?php

namespace App\Services\Activity;

use App\Models\Currency\Currency;
use App\Models\Recipe\Recipe;
use App\Models\User\UserCurrency;
use App\Models\User\UserItem;
use App\Services\CurrencyManager;
use App\Services\InventoryManager;
use App\Services\RecipeManager;
use App\Services\Service;

use DB;

class CraftingService extends Service {

  /**
   * Retrieves any data that should be used in the activity module editing form on the admin side
   *
   * @return array
   */
  public function getEditData() {

    return [
      'recipes' => Recipe::orderBy('name', 'ASC')->pluck('name', 'id'),
    ];
  }

  /**
   * Retrieves any data that should be used in the activity module on the user side
   *
   * @return array
   */
  public function getActData($activity) {

    return [
      'recipes' => Recipe::whereIn('id', $activity->data)->get()
    ];
  }

  /**
   * Processes the data attribute of the activity and returns it in the preferred format.
   *
   * @param  string  $tag
   * @return mixed
   */
  public function getData($data) {
    return json_decode($data);
  }

  /**
   * Processes the data attribute of the activity and returns it in the preferred format.
   *
   * @param  object  $activity
   * @param  array   $data
   * @return bool
   */
  public function updateData($activity, $data) {
    return $data['recipe_id'];
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
      $recipe = Recipe::where('id', $data['recipe_id'])->first();
      $isComplete = $this->checkRecipe($user, $recipe);
      if (!$isComplete) throw new \Exception('You haven\'t gotten all the items yet for this recipe.');

      // Complete the recipe ----  NOTE: This could be done with less code duplication with RecipeManager but hrmmm would be duplicative processing also.
      // Check for sufficient currencies
      $user_currencies = $user->getCurrencies(true);
      $currency_ingredients = $recipe->ingredients->where('ingredient_type', 'Currency');
      foreach ($currency_ingredients as $ingredient) {
        $currency = $user_currencies->where('id', $ingredient->data[0])->first();
        if ($currency->quantity < $ingredient->quantity) throw new \Exception('Insufficient currency.');
      }

      // Check for sufficient ingredients
      $manager = new RecipeManager;
      $ingredients = $manager->pluckIngredients($user, $recipe);
      if (!$ingredients) throw new \Exception('Insufficient ingredients.');
      // Debit the ingredients
      $service = new InventoryManager();
      foreach ($ingredients as $id => $quantity) {
        $stack = UserItem::find($id);
        if (!$service->debitStack($user, 'Crafting', ['data' => 'Used in ' . $recipe->name . ' Recipe'], $stack, $quantity)) throw new \Exception('Items could not be removed.');
      }
      // Debit the currency
      $service = new CurrencyManager();
      foreach ($currency_ingredients as $ingredient) {
        if (!$service->debitCurrency($user, null, 'Crafting', 'Used in ' . $recipe->name . ' Recipe', Currency::find($ingredient->data[0]), $ingredient->quantity)) throw new \Exception('Currency could not be debited.');
      }

      // Credit rewards
      $logType = 'Crafting Reward';
      $craftingData = [
        'data' => 'Received rewards from ' . $recipe->displayName . ' recipe'
      ];

      if (!$rewards = fillUserAssets($recipe->rewardItems, null, $user, $logType, $craftingData)) throw new \Exception("Failed to distribute rewards to user.");

      flash(getRewardsString($rewards));

      return $this->commitReturn(true);
    } catch (\Exception $e) {
      $this->setError('error', $e->getMessage());
    }
    return $this->rollbackReturn(false);
  }

  public function checkRecipe($user, $recipe) {
    $completed = true;
    foreach ($recipe->ingredients as $ingredient) {
      if ($ingredient->ingredient_type === 'Item')
        $userOwned = UserItem::where('user_id', $user->id)->where('item_id', $ingredient->ingredient->id)->where('count', '>', 0)->sum('count');
      elseif ($ingredient->ingredient_type === 'Currency')
        $userOwned = UserCurrency::where('user_id', $user->id)->where('currency_id', $ingredient->ingredient->id)->sum('quantity');

      if (intval($userOwned) < $ingredient->quantity) $completed = false;
    }

    return $completed;
  }
}
