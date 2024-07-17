<?php namespace App\Services;

use App\Services\Service;

use DB;
use Notifications;
use Config;

use App\Models\User\User;
use App\Models\User\UserItem;
use App\Models\User\UserCurrency;
use App\Models\User\UserCollection;

use App\Models\Collection\Collection;
use App\Models\Collection\CollectionIngredient;
use App\Models\Collection\CollectionReward;

use App\Models\Currency\Currency;

use App\Services\InventoryManager;
use App\Services\CurrencyManager;

class CollectionManager extends Service
{

/**********************************************************************************************
     Collection completion
 **********************************************************************************************/

    /**
     * Attempts to complete the specified collection.
    *
    * @param  array                        $data
    * @param  \App\Models\Collection\Collection    $collection
    * @param  \App\Models\User\User        $user
    * @return bool
    */
    public function completeCollection($data, $collection, $user)
    {
        DB::beginTransaction();

        try {
            // Check user has all limits
            
            if($collection->parent_id)
                {
                    $completed = UserCollection::where('user_id', $user->id)->where('collection_id', $collection->parent_id)->count();    
                    if(!$completed) throw new \Exception('Please complete the prerequisite first.');
                }
            // Check for sufficient currencies
            $user_currencies = $user->getCurrencies(true);
            $currency_ingredients = $collection->ingredients->where('ingredient_type', 'Currency');
            foreach($currency_ingredients as $ingredient) {
                $currency = $user_currencies->where('id', $ingredient->data[0])->first();
                if($currency->quantity < $ingredient->quantity) throw new \Exception('Insufficient currency.');
            }

            // If there are non-Currency ingredients.
            if(isset($data['stack_id']))
            {
                // Fetch the stacks from DB
                $stacks = UserItem::whereIn('id', $data['stack_id'])->get()->map(function($stack) use ($data) {
                    $stack->count = (int)$data['stack_quantity'][$stack->id];
                    return $stack;
                });

                // Check for sufficient ingredients
                $plucked = $this->pluckIngredients($user, $collection, $stacks);
                if(!$plucked) throw new \Exception('Insufficient ingredients selected.');

                // Debit the ingredients
                //$service = new InventoryManager();
               // foreach($plucked as $id => $quantity) {
                   // $stack = UserItem::find($id);
                   // if(!$service->debitStack($user, 'Collection', ['data' => 'Used in '.$collection->name.' Collection'], $stack, $quantity)) throw new \Exception('Items could not be removed.');
               // }
            } else {
                $items = $collection->ingredients->where('ingredient_type', 'Item');
                if (count($items) > 0) throw new \Exception('Insufficient ingredients selected.');
            }

            // Debit the currency
           // $service = new CurrencyManager();
           // foreach($currency_ingredients as $ingredient) {
                //if(!$service->debitCurrency($user, null, 'Collection', 'Used in '.$collection->name.' Collection', Currency::find($ingredient->data[0]), $ingredient->quantity)) throw new \Exception('Currency could not be debited.');
           // }

            // Credit rewards
            $logType = 'Collection Reward';
            $collectionData = [
                'data' => 'Received rewards from '. $collection->displayName .' collection'
            ];

            if(!fillUserAssets($collection->rewardItems, null, $user, $logType, $collectionData)) throw new \Exception("Failed to distribute rewards to user.");

            // Credit rewards
            $servicetwo = new \App\Services\CollectionService;
            $logTypetwo = 'Collection Completed';
            $collectionDatatwo = [
                'data' => 'Completed '. $collection->displayName .' collection'
            ];
            if(!$servicetwo->creditCollection(null, $user, null, $logTypetwo, $collectionDatatwo, $collection)) throw new \Exception('Failed to create collection log.');



            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Plucks stacks from a given Collection of user items that meet the collection requirements of a collection
    * If there are insufficient ingredients, null is returned
    *
    * @param  \Illuminate\Database\Eloquent\Collection     $user_items
    * @param  \App\Models\Collection\Collection                    $collection
    * @return array|null
    */
    public function pluckIngredients($user, $collection, $selectedStacks = null)
    {
        $user_items = UserItem::with('item')->whereNull('deleted_at')->where('count', '>', '0')->where('user_id', $user->id)->get();
        $plucked = [];
        // foreach ingredient, search for a qualifying item, and select items up to the quantity, if insufficient continue onto the next entry
        foreach($collection->ingredients->sortBy('ingredient_type') as $ingredient)
        {
            if($selectedStacks) {
                switch($ingredient->ingredient_type)
                {
                    case 'Item':
                        $stacks = $selectedStacks->where('item.id', $ingredient->data[0]);
                        break;
                    case 'MultiItem':
                        $stacks = $selectedStacks->whereIn('item.id', $ingredient->data);
                        break;
                    case 'Category':
                        $stacks = $selectedStacks->where('item.item_category_id', $ingredient->data[0]);
                        break;
                    case 'MultiCategory':
                        $stacks = $selectedStacks->whereIn('item.item_category_id', $ingredient->data);
                        break;
                    case 'Currency':
                        continue 2;
                }
            }
            else {
                switch($ingredient->ingredient_type)
                {
                    case 'Item':
                        $stacks = $user_items->where('item.id', $ingredient->data[0]);
                        break;
                    case 'MultiItem':
                        $stacks = $user_items->whereIn('item.id', $ingredient->data);
                        break;
                    case 'Category':
                        $stacks = $user_items->where('item.item_category_id', $ingredient->data[0]);
                        break;
                    case 'MultiCategory':
                        $stacks = $user_items->whereIn('item.item_category_id', $ingredient->data);
                        break;
                    case 'Currency':
                        continue 2;
                }
            }

            $quantity_left = $ingredient->quantity;
            while($quantity_left > 0 && count($stacks) > 0)
            {
                $stack = $stacks->pop();
                $plucked[$stack->id] = $stack->count >= $quantity_left ? $quantity_left : $stack->count;
                // Update the larger collection
                $user_items = $user_items->map(function($s) use($stack, $plucked) {
                    if($s->id == $stack->id) $s->count -= $plucked[$stack->id];
                    if($s->count) return $s;
                    else return null;
                })->filter();
                $quantity_left -= $plucked[$stack->id];
            }
            // If there are no more eligible ingredients but the requirement is not fulfilled, the pluck fails
            if($quantity_left > 0) return null;
        }
        return $plucked;
    }
}