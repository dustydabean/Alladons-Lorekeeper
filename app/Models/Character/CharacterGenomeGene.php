<?php

namespace App\Models\Character;

use Config;
use DB;
use App\Models\Model;
use App\Models\Character\CharacterCategory;

class CharacterGenomeGene extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'character_genome_id', 'loci_allele_id', 'loci_id',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'character_genome_genes';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the image associated with this record.
     */
    public function genome()
    {
        return $this->belongsTo('App\Models\Character\CharacterGenome');
    }

    /**
     * Get the image associated with this record.
     */
    public function allele()
    {
        return $this->belongsTo('App\Models\Genetics\LociAllele', 'loci_allele_id');
    }

    /**
     * Get the image associated with this record.
     */
    public function loci()
    {
        return $this->belongsTo('App\Models\Genetics\Loci');
    }
}
