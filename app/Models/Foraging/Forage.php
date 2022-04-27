<?php

namespace App\Models\Foraging;

use Config;
use App\Models\Model;

class Forage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'display_name', 'is_active', 'has_image'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'forages';
    
    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'name' => 'required',
        'display_name' => 'required',
    ];
    
    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name' => 'required',
        'display_name' => 'required',
    ];

    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/

    /**
     * Get the loot data for this loot table.
     */
    public function loot() 
    {
        return $this->hasMany('App\Models\Foraging\ForageReward');
    }

    /**********************************************************************************************
    
        ACCESSORS

    **********************************************************************************************/

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute()
    {
        return 'images/data/foraging';
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getImageFileNameAttribute()
    {
        return $this->id . '-forage.png';
    }

    /**
     * Gets the path to the file directory containing the model's image.
     *
     * @return string
     */
    public function getImagePathAttribute()
    {
        return public_path($this->imageDirectory);
    }

    /**
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        if (!$this->has_image) return null;
        return asset($this->imageDirectory . '/' . $this->imageFileName);
    }
    
    /**
     * Displays the model's name, linked to its encyclopedia page.
     *
     * @return string
     */
    public function getFancyDisplayNameAttribute()
    {
        return '<span class="display-loot">'.$this->attributes['display_name'].'</span> ';
    }

    /**
     * Gets the loot table's asset type for asset management.
     *
     * @return string
     */
    public function getAssetTypeAttribute()
    {
        return 'loot_tables';
    }

    /**********************************************************************************************
    
        OTHER FUNCTIONS

    **********************************************************************************************/
    
    /**
     * Rolls on the loot table and consolidates the rewards.
     *
     * @param  int  $quantity
     * @return \Illuminate\Support\Collection
     */
    public function roll($quantity = 1) 
    {
        $rewards = createAssetsArray();

        $loot = $this->loot;
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
                else addAsset($rewards, $result->reward, $result->quantity);
            }
        }
        return $rewards;
    }
}
