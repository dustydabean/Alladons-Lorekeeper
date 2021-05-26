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
        'visibility_level',
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

    /**
     * Gets all the loci present in this character's genome.
     */
    public function getLoci()
    {
        $array = array_values(array_unique($this->genes->pluck('loci_id')->toArray()));
        foreach(array_unique($this->gradients->pluck('loci_id')->toArray()) as $value) array_push($array, $value);
        foreach(array_unique($this->numerics->pluck('loci_id')->toArray()) as $value) array_push($array, $value);
        $loci = Loci::whereIn('id', $array)->orderBy('sort', 'desc')->get();
        return $loci;
    }

    /**
     * Gets the genes from a loci.
     *
     * @return  Illuminate\Database\Eloquent\Collection
     */
    public function getGenes($loci)
    {
        $genes = $this->getGenesOfType($loci->type);
        if ($genes == null) return null;
        $genes = $genes->where('loci_id', $loci->id);
        return ($genes->count() > 0) ? $genes : null;
    }

    /**
     * Gets the genes collection by type.
     *
     * @return  Illuminate\Database\Eloquent\Collection
     */
    public function getGenesOfType($type)
    {
        if ($type == "gene") return $this->genes;
        if ($type == "gradient") return $this->gradients;
        if ($type == "numeric") return $this->numerics;
        return null;
    }

    /**
     * Checks if this genome contains a particular locus.
     */
    public function hasLocus($loci)
    {
        $genes = $this->getGenesOfType($loci->type);
        if ($genes == null) return false;
        return $genes->where('loci_id', $loci->id)->count() > 0;
    }

    /**
     * Cross this with another genome. Used for previews.
     */
    public function crossWith($sire)
    {
        $genes = [];

        // Go through each loci
        foreach (Loci::query()->orderBy('sort', 'desc')->get() as $loci)
        {
            if ($this->hasLocus($loci) || $sire->hasLocus($loci))
            {
                $gene = $this->inheritGene($loci, $this->getGenes($loci), $sire->getGenes($loci));
                if($gene) array_push($genes, "<span class='text-monospace mx-1' data-toggle='tooltip' title='". $loci->name ."'>". $gene ."</span>");
            }
        }

        return $genes;
    }

    /**
     * Figures out how to inherit a loci. Used for previews.
     *
     * @param   \App\Models\Genetics\Loci                $loci
     * @param   Illuminate\Database\Eloquent\Collection  $damGenes
     * @param   Illuminate\Database\Eloquent\Collection  $sireGenes
     * @return  array
     */
    private function inheritGene($loci, $damGenes, $sireGenes)
    {
        if ($loci->type == "gene") {
            $alleles = "";

            // Matrilineal gene
            if ($damGenes == null) $alleles.= $loci->allelesReversed->first()->display_name;
            else $alleles.= $damGenes->random()->allele->display_name;

            // Patrilineal gene
            if ($sireGenes == null) $alleles.= $loci->allelesReversed->first()->display_name;
            else $alleles.= $sireGenes->random()->allele->display_name;

            return $alleles;
        } elseif ($loci->type == "gradient") {
            $rufus = "";
            $dam = ""; $sire = "";

            // Matrilineal gene
            if ($damGenes == null) for($i = 0; $i < $loci->length; $i++) $dam.="-";
            else $dam = $damGenes->random()->display_genome;

            // Patrilineal gene
            if ($sireGenes == null) for($i = 0; $i < $loci->length; $i++) $sire.="-";
            else $sire = $sireGenes->random()->display_genome;

            // Let's see if I remember how to do this...
            for ($i = 0; $i < $loci->length/2; $i++) {
                $rufus .= substr($dam, ($i * 2) + mt_rand(0, 1), 1)
                        . substr($sire, ($i * 2) + mt_rand(0, 1), 1);
            }

            return $rufus;
        } elseif ($loci->type == "numeric") {
            $fromMother = mt_rand(0, 1) == 0;
            if ($damGenes == null) return $sireGenes->random()->value;
            if ($sireGenes == null) return $damGenes->random()->value;
            return $fromMother ? $damGenes->random()->value : $sireGenes->random()->value;
        }
    }
}
