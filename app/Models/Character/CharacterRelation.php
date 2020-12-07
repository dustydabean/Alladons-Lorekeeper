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
        'chara_1', 'chara_2', 'info', 'type', 'status'
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

    public function character()
    {
        return $this->belongsTo('App\Models\Character\Character', 'chara_2');
    }

    public function otherChara()
    {
        return $this->hasOne('App\Models\Character\CharacterRelation', 'chara_1', 'chara_2')->where('chara_1', $this->chara_2);
    }

    public function inverse()
    {
        return $this->hasOne('App\Models\Character\CharacterRelation', 'chara_1', 'chara_2')->where('chara_1', $this->chara_2)->where('chara_2', $this->chara_1);
    }
}