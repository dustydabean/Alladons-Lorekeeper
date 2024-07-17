<?php

namespace App\Models\Collection;

use App;
use Config;
use App\Models\Model;

class CollectionReward extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'collection_id', 'rewardable_type', 'rewardable_id', 'quantity'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'collection_rewards';

    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'rewardable_type' => 'required',
        'rewardable_id' => 'required',
        'quantity' => 'required|integer|min:1'
    ];

    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'rewardable_type' => 'required',
        'rewardable_id' => 'required',
        'quantity' => 'required|integer|min:1'
    ];

    /**********************************************************************************************
    
        RELATIONS
    **********************************************************************************************/

    /**
     * Get the reward attached to the loot entry.
     */
    public function reward() 
    {
        switch ($this->rewardable_type)
        {
            case 'Item':
                return $this->belongsTo('App\Models\Item\Item', 'rewardable_id');
            case 'Currency':
                return $this->belongsTo('App\Models\Currency\Currency', 'rewardable_id');
            case 'LootTable':
                return $this->belongsTo('App\Models\Loot\LootTable', 'rewardable_id');
            case 'None':
                // Laravel requires a relationship instance to be returned (cannot return null), so returning one that doesn't exist here.
                return $this->belongsTo('App\Models\Collection\CollectionRewards', 'rewardable_id', 'collection_id')->whereNull('collection_id');
        }
        return null;
    }
}