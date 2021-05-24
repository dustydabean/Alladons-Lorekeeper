<?php

namespace App\Models\Genetics;

use Config;
use DB;
use App\Models\Model;
use App\Models\Feature\FeatureCategory;
use App\Models\Species\Species;
use App\Models\Rarity;
use Illuminate\Validation\Rule;

class LociAllele extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'loci_id', 'is_dominant', 'sort', 'name', 'modifier',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'loci_alleles';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the rarity of this feature.
     */
    public function loci()
    {
        return $this->belongsTo('App\Models\Genetics\Loci');
    }

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to sort by sort order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortByDominance($query)
    {
        return $query->orderBy('is_dominant')->orderBy('sort');
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Displays the model's name, linked to its encyclopedia page.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        $name = $this->name . "<sup>" . $this->modifier . "</sup>";
        return $this->is_dominant ? $name : strtolower($name);
    }

    /**
     * Displays the model's name, linked to its encyclopedia page.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        $name = $this->name . (($this->modifier && $this->modifier != "") ? "(" . $this->modifier . ")" : "");
        return $this->is_dominant ? $name : strtolower($name);
    }
}
