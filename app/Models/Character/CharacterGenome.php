<?php

namespace App\Models\Character;

use Config;
use DB;
use App\Models\Model;
use App\Models\Character\CharacterCategory;
use App\Models\Genetics\Loci;

class CharacterGenome extends Model
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
        return $this->hasMany('App\Models\Character\CharacterGenomeGene');//->groupBy('loci_id');
    }

    /**
     * Get the image associated with this record.
     */
    public function gradients()
    {
        return $this->hasMany('App\Models\Character\CharacterGenomeGradient');//->groupBy('loci_id');
    }

    /**
     * Get the image associated with this record.
     */
    public function numerics()
    {
        return $this->hasMany('App\Models\Character\CharacterGenomeNumeric');//->groupBy('loci_id');
    }

    public function getLoci()
    {
        $array = array_values(array_unique($this->genes->pluck('loci_id')->toArray()));
        foreach(array_unique($this->gradients->pluck('loci_id')->toArray()) as $value) array_push($array, $value);
        foreach(array_unique($this->numerics->pluck('loci_id')->toArray()) as $value) array_push($array, $value);
        $loci = Loci::whereIn('id', $array)->orderBy('sort', 'desc')->get();
        return $loci;
    }
}
