<?php

namespace App\Models\Genetics;

use Config;
use DB;
use App\Models\Model;
use App\Models\Feature\FeatureCategory;
use App\Models\Species\Species;
use App\Models\Rarity;
use Illuminate\Validation\Rule;

class Loci extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'type', 'length', 'chromosome', 'sort',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'locis';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the rarity of this feature.
     */
    public function alleles()
    {
        return $this->hasMany('App\Models\Genetics\LociAllele')->orderBy('is_dominant', 'desc')->orderBy('sort', 'asc');
    }

    /**
     * Gets the alleles of this feature, sorted by LEAST dominant.
     */
    public function allelesReversed()
    {
        return $this->hasMany('App\Models\Genetics\LociAllele')->orderBy('is_dominant', 'asc')->orderBy('sort', 'desc');
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
    public function scopeSortBySortOrder($query)
    {
        return $query->orderBy('sort');
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
        return '<a href="'.$this->url.'" class="display-trait">'.$this->name.'</a>';
    }

    /**
     * Gets the URL of the model's encyclopedia page.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('world/traits?name='.$this->name);
    }

    /**
     * Gets the URL for a masterlist search of characters in this category.
     *
     * @return string
     */
    public function getSearchUrlAttribute()
    {
        return url('masterlist?feature_id[]='.$this->id);
    }

    public function getAlleles()
    {
        if($this->type != "gene") return [];
        return LociAllele::selectRaw('id, if(modifier is not null and modifier != \'\', if(is_dominant is true, concat(name, \'(\', modifier, \')\'), lower(concat(name, \'(\', modifier, \')\'))), if(is_dominant is true, name, lower(name))) as name')->where('loci_id', $this->id)
                ->orderBy('is_dominant', 'desc')->orderBy('sort', 'asc')
                ->pluck('name', 'id')->toArray();
    }
}
