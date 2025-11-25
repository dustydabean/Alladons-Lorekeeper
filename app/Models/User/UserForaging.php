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
        'user_id', 'forage_id', 'foraged_at', 'character_id', 'distribute_at'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_foraging';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'foraged_at' => 'datetime',
        'distribute_at' => 'datetime',
    ];

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
        return $this->belongsTo('App\Models\Foraging\Forage');
    }

    /**
     * Get the selected character for this foraging session.
     */
    public function character()
    {
        return $this->belongsTo('App\Models\Character\Character');
    }
}