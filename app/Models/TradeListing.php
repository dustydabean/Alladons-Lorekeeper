<?php

namespace App\Models;

use Config;
use DB;
use Carbon\Carbon;
use Settings;

use App\Models\Character\Character;

use App\Models\Model;

class TradeListing extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'comments', 'contact', 'data', 'expires_at'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trade_listings';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;

    /**
     * Dates on the model to convert to Carbon instances.
     *
     * @var array
     */
    public $dates = ['expires_at'];

    /**
     * Validation rules for character creation.
     *
     * @var array
     */
    public static $createRules = [
        'comments' => 'nullable',
        'contact' => 'required',
        'seeking_etc' => 'nullable|between:3,100',
        'offering_etc' => 'nullable|between:3,100',
    ];
    
    /**
     * Validation rules for character updating.
     *
     * @var array
     */
    public static $updateRules = [
        'comments' => 'nullable',
        'contact' => 'required',
        'seeking_etc' => 'nullable|between:3,100',
        'offering_etc' => 'nullable|between:3,100',
    ];

    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/

    /**
     * Get the user who posted the trade listing.
     */
    public function user() 
    {
        return $this->belongsTo('App\Models\User\User', 'user_id');
    }

    /**********************************************************************************************
    
        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to only include active trade listings.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where(function($query) {
                $query->where('expires_at', '>', Carbon::now())->orWhere(function($query) {
                    $query->where('expires_at', '>=', Carbon::now());
                });
        });
        
    }

    /**
     * Scope a query to only include active trade listings.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpired($query)
    {
        return $query->where(function($query) {
                $query->where('expires_at', '<', Carbon::now())->orWhere(function($query) {
                    $query->where('expires_at', '<=', Carbon::now());
                });
        });
        
    }

    /**********************************************************************************************
    
        ACCESSORS

    **********************************************************************************************/

    /**
     * Gets the Display Name of the trade listing.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return $this->user->displayName .'\'s <a href="'. $this->url. '">Trade Listing</a> (#' . $this->id .')';
    }
    
    /**
     * Check if the trade listing is active.
     *
     * @return bool
     */
    public function getIsActiveAttribute()
    {
        if($this->expires_at >= Carbon::now()) return true;

        return false;
    }

    /**
     * Get the data attribute as an associative array.
     *
     * @return array
     */
    public function getDataAttribute()
    {
        return json_decode($this->attributes['data'], true);
    }

    /**
     * Gets the URL of the trade listing.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('trades/listings/'.$this->id);
    }

    /**********************************************************************************************
    
        OTHER FUNCTIONS

    **********************************************************************************************/
    
    /**
     * Gets all characters involved in the trade listing.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCharacterData()
    {
        return Character::with('user')->whereIn('id', $this->getCharacters($this->user))->get();
    }

    /**
     * Gets the inventory of the user for selection.
     *
     * @return array
     */
    public function getInventory($user)
    {
        return $this->data && isset($this->data['user']['user_items']) ? $this->data['user']['user_items'] : [];
        return $inventory;
    }

    /**
     * Gets the currencies of the given user for selection.
     *
     * @param  \App\Models\User\User $user
     * @return array
     */
    public function getCurrencies($user)
    {
        return $this->data && isset($this->data['user']) && isset($this->data['user']['currencies']) ? $this->data['user']['currencies'] : [];
    }

    /**
     * Gets the characters of the given user for selection.
     *
     * @param  \App\Models\User\User $user
     * @return array
     */
    public function getCharacters($user)
    {

        $characters = $this->data && isset($this->data['user']) && isset($this->data['user']['characters']) ? $this->data['user']['characters'] : [];
        if($characters) $characters = array_keys($characters);
        return $characters;
    }

}
