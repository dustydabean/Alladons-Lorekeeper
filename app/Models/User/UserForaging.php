<?php

namespace App\Models\Foraging;

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
        'user_id', 'last_foraged_at'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_foraging';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = false;

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
}