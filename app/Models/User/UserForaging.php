<?php

namespace App\Models\User;

use Carbon\Carbon;
use Config;
use App\Models\Model;

class UserForaging extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = [
        'user_id', 'last_forage_id', 'last_foraged_at', 'distribute_at', 'reset_at'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_foraging';

    /**
     * Dates on the model to convert to Carbon instances.
     *
     * @var array
     */
     public $dates = ['last_foraged_at', 'distribute_at', 'reset_at'];

    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/

    /**
     * Get the user this set of settings belongs to.
     */
     public function user() 
     {
         return $this->belongsTo('App\Models\User\User');
     }

     /**
      * Get current forage 
      */
    public function forage() 
    {
        if($this->last_forage_id) 
            return $this->belongsTo('App\Models\Foraging\Forage', 'last_forage_id');
    }
}