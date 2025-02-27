<?php

namespace App\Models\Character;

use App\Models\Model;

class CharacterGenomeGradient extends Model {
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
    protected $table = 'character_genome_gradients';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the genome associated with this record.
     */
    public function genome() {
        return $this->belongsTo('App\Models\Character\CharacterGenome');
    }

    /**
     * Get the loci associated with this record.
     */
    public function loci() {
        return $this->belongsTo('App\Models\Genetics\Loci');
    }

    /**********************************************************************************************

        FUNCTIONS

    **********************************************************************************************/

    /**
     * Gets the numeric value of this gene (number of "+" genes).
     */
    public function getDisplayValueAttribute() {
        return substr_count($this->value, '1');
    }

    /**
     * Turns binary into human readable string of "+" and "-".
     */
    public function getDisplayGenomeAttribute() {
        $string = preg_replace(['/1/', '/0/'], ['+', '-'], $this->value);
        while (strlen($string) < $this->loci->length) {
            $string .= '-';
        }

        return $string;
    }
}
