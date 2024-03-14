<?php

namespace App\Models\Character;

use App\Models\Model;

class CharacterBreedingLogRelation extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'log_id', 'character_id', 'is_parent', 'twin_id', 'chimerism',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'character_breeding_log_relations';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the character associated with this record.
     */
    public function character()
    {
        return $this->belongsTo('App\Models\Character\Character');
    }

    /**
     * Get the character associated with this record.
     */
    public function twin()
    {
        return $this->belongsTo('App\Models\Character\Character', 'twin_id');
    }

    /**
     * Get the breeding log associated with this record.
     */
    public function log()
    {
        return $this->belongsTo('App\Models\Character\CharacterBreedingLog', 'log_id');
    }
}
