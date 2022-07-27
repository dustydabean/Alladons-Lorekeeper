<?php

namespace App\Models\User;

use App\Models\Model;
use Carbon\Carbon;

class UserFriend extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'initiator_id', 'recipient_id', 'recipient_approval', 'created_at', 'accepted_at',
    ];

    /**
     * Dates on the model to convert to Carbon instances.
     *
     * @var array
     */
    protected $dates = ['created_at', 'accepted_at'];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the user who created the friendship.
     */
    public function initiator()
    {
        return $this->belongsTo('App\Models\User\User', 'initiator_id');
    }

    /**
     * Get the user who is blocked.
     */
    public function recipient()
    {
        return $this->belongsTo('App\Models\User\User', 'recipient_id');
    }

    /**********************************************************************************************

        ATTRIBUTES

    **********************************************************************************************/

    /**
     * gets the opposite of the user passed in.
     *
     * @param mixed $id
     */
    public function other($id)
    {
        if ($this->initiator_id == $id) {
            return $this->recipient;
        } else {
            return $this->initiator;
        }
    }
}
