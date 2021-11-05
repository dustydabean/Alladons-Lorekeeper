<?php

namespace App\Models\Character;

use Config;
use App\Models\Model;

class BreedingPermissionLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'breeding_permission_id', 'sender_id', 'recipient_id', 'log', 'log_type', 'data'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'breeding_permissions_log';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the user who initiated the logged action.
     */
    public function sender()
    {
        return $this->belongsTo('App\Models\User\User', 'sender_id');
    }

    /**
     * Get the user who received the logged action.
     */
    public function recipient()
    {
        return $this->belongsTo('App\Models\User\User', 'recipient_id');
    }

    /**
     * Get the breeding permission that is the target of the action.
     */
    public function breedingPermission()
    {
        return $this->belongsTo('App\Models\Character\BreedingPermission');
    }

}
