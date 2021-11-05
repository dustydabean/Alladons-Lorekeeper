<?php

namespace App\Models\Character;

use App\Models\Model;

class BreedingPermission extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'character_id', 'recipient_id', 'type', 'is_used', 'description'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'breeding_permissions';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;

    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'recipient_id' => 'required',
        'type' => 'required',
        'description' => 'string|nullable|max:500'
    ];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the character the breeding permission belongs to.
     */
    public function character()
    {
        return $this->belongsTo('App\Models\Character\Character');
    }

    /**
     * Get the recipient of the breeding permission.
     */
    public function recipient()
    {
        return $this->belongsTo('App\Models\User\User', 'recipient_id');
    }

    /**********************************************************************************************

        OTHER FUNCTIONS

    **********************************************************************************************/

    /**
     * Get the breeding permission's ownership logs.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOwnershipLogs()
    {
        $query = BreedingPermissionLog::with('sender.rank')->with('recipient.rank')->where('breeding_permission_id', $this->id)->orderBy('id', 'DESC');
        return $query->get();
    }
}
