<?php

namespace App\Models\Foraging;

use Config;
use App\Models\Model;
use Carbon\Carbon;

class Forage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'display_name', 'is_active', 'has_image', 'active_until', 'stamina_cost', 'has_cost', 'currency_id', 'currency_quantity'
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
     * Get the loot data for this forage table.
     */
    public function loot() 
    {
        return $this->hasMany('App\Models\Foraging\ForageReward');
    }

    /**
     * Get the currency for this forage table.
     */
    public function currency()
    {
        return $this->belongsTo('App\Models\Currency\Currency', 'currency_id');
    }

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * scopes all active forages that are within the active_until timestamp, unless a user is staff
     */
    public function scopeVisible($query, $isStaff = false)
    {
        if ($isStaff) return $query;
        else return $query->where('is_active', 1)
        ->where(function($query) {
            $query->whereNull('active_until')->orWhere('active_until', '>', Carbon::now());
        });
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

    /**
     * returns if the table is visible or not (bool)
     */
    public function getIsVisibleAttribute()
    {
        if (!$this->is_active) return false;
        // check if active_until is passed
        if ($this->active_until && $this->active_until < Carbon::now()) return false;
        return true;
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
        return rollRewards($this->loot, $quantity);
    }
}
