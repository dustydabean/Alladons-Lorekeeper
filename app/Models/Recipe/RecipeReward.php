<?php

namespace App\Models\Recipe;

use App\Models\Currency\Currency;
use App\Models\Item\Item;
use App\Models\Loot\LootTable;
use App\Models\Model;

class RecipeReward extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'recipe_id', 'rewardable_type', 'rewardable_id', 'quantity',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'recipe_rewards';

    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'rewardable_type' => 'required',
        'rewardable_id'   => 'required',
        'quantity'        => 'required|integer|min:1',
    ];

    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'rewardable_type' => 'required',
        'rewardable_id'   => 'required',
        'quantity'        => 'required|integer|min:1',
    ];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the reward attached to the loot entry.
     */
    public function reward() {
        switch ($this->rewardable_type) {
            case 'Item':
                return $this->belongsTo(Item::class, 'rewardable_id');
            case 'Currency':
                return $this->belongsTo(Currency::class, 'rewardable_id');
            case 'LootTable':
                return $this->belongsTo(LootTable::class, 'rewardable_id');
            case 'None':
                // Laravel requires a relationship instance to be returned (cannot return null), so returning one that doesn't exist here.
                return $this->belongsTo(self::class, 'rewardable_id', 'recipe_id')->whereNull('recipe_id');
        }

        return null;
    }
}
