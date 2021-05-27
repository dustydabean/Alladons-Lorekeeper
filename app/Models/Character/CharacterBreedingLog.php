<?php

namespace App\Models\Character;

use App\Models\Model;

class CharacterBreedingLog extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'roller_settings', 'rolled_at',
        'user_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'roller_settings' => 'array',
    ];

    /**
     * Dates on the model to convert to Carbon instances.
     *
     * @var array
     */
    protected $dates = ['rolled_at'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'character_breeding_logs';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the image associated with this record.
     */
    public function loggedCharacters()
    {
        return $this->hasMany('App\Models\Character\CharacterBreedingLogRelation', 'log_id');
    }

    /**
     * Get the image associated with this record.
     */
    public function parents()
    {
        return $this->loggedCharacters()->where('is_parent', true);
    }

    /**
     * Get the image associated with this record.
     */
    public function children()
    {
        return $this->loggedCharacters()->where('is_parent', false);
    }

    /**
     * Get the user associated with this record. (Staff member who rolled it.)
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User\User');
    }
}
