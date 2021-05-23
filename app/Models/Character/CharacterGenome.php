<?php

namespace App\Models\Character;

use Config;
use DB;
use App\Models\Model;
use App\Models\Character\CharacterCategory;

class CharacterFeature extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'character_id',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'character_genomes';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the image associated with this record.
     */
    public function character()
    {
        return $this->belongsTo('App\Models\Character\Character');
    }

    /**
     * Get the image associated with this record.
     */
    public function genes()
    {
        return $this->hasMany('App\Models\Character\CharacterGenomeGenes')->groupBy('loci_id');
    }

    /**
     * Get the image associated with this record.
     */
    public function gradients()
    {
        return $this->hasMany('App\Models\Character\CharacterGenomeGradients')->groupBy('loci_id');
    }

    /**
     * Get the image associated with this record.
     */
    public function numerics()
    {
        return $this->hasMany('App\Models\Character\CharacterGenomeNumerics')->groupBy('loci_id');
    }
}
