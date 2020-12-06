<?php

namespace App\Models\Character;

use Config;
use App\Models\Model;
use App\Models\Character\Character;

class CharacterRelation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'data', 'info', 'type', 'status'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'character_relations';

    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/

    /**
     * Get the character that is the target of the action.
     */
    public function characters() 
    {
        return $this->belongsToMany('App\Models\Character\Character');
    }


}