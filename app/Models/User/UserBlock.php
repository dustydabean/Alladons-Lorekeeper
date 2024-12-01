<?php

namespace App\Models\User;

use App\Models\Model;
use Carbon\Carbon;

class UserBlock extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'blocked_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the user who created the block.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User\User');
    }

    /**
     * Get the user who is blocked.
     */
    public function blocked()
    {
        return $this->belongsTo('App\Models\User\User', 'blocked_id');
    }
}
