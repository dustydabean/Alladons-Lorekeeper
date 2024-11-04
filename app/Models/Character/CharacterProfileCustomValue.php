<?php

namespace App\Models\Character;

use App\Models\Model;

class CharacterProfileCustomValue extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'character_id', 'group', 'name', 'data', 'data_parsed',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'character_profile_custom_values';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the character this profile belongs to.
     */
    public function profile() {
        return $this->belongsTo('App\Models\Character\CharacterProfile', 'character_id', 'character_id');
    }

    /**
     * Get the character this profile belongs to.
     */
    public function character() {
        return $this->belongsTo('App\Models\Character\Character', 'character_id', 'id');
    }
}
