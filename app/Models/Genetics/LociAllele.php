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
        'summary', 'is_visible',
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
     * Get the gene group of this loci.
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

    /**
     * Scope a query to only include visible.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', 1);
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Gets the Allele display name for HTML contexts (with superscripted modifier).
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        $name = $this->name . "<sup>" . $this->modifier . "</sup>";
        return $this->is_dominant ? $name : strtolower($name);
    }

    /**
     * Gets the Allele display name for non-HTML contexts (such as dropdown menus).
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        $name = $this->name . (($this->modifier && $this->modifier != "") ? "(" . $this->modifier . ")" : "");
        return $this->is_dominant ? $name : strtolower($name);
    }
}
