<?php

namespace App\Models\Character;

use Config;
use DB;
use App\Models\Model;
use App\Models\Character\CharacterCategory;

class CharacterGenomeNumeric extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'character_genome_id', 'loci_id', 'value',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'character_genome_numerics';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the genome associated with this record.
     */
    public function genome()
    {
        return $this->belongsTo('App\Models\Character\CharacterGenome');
    }

    /**
     * Get the loci associated with this record.
     */
    public function loci()
    {
        return $this->belongsTo('App\Models\Genetics\Loci');
    }

    /**
     * Estimated value attribute.
     */
    public function getEstValueAttribute() {
        $frag = $this->loci->length / 3;
        return ($this->value <= $frag) ? "0-" . ceil($frag) : (($this->value <= ceil($frag * 2)) ? floor($frag)."-".ceil($frag * 2) : floor($frag * 2).$this->loci->length);
    }
}
