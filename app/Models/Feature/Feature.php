<?php

namespace App\Models\Feature;

use App\Models\Model;
use App\Models\Rarity;
use App\Models\Species\Species;
use App\Models\Species\Subtype;
use Illuminate\Support\Facades\DB;

class Feature extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'feature_category_id', 'species_id', 'subtype_id', 'rarity_id', 'name', 'has_image', 'description', 'parsed_description', 'is_visible', 'hash', 'has_example_image', 'example_hash', 'example_summary',
        'mut_level', 'mut_type', 'is_locked', 'code_id',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'features';
    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'feature_category_id' => 'nullable',
        'species_id'          => 'nullable',
        'subtype_id'          => 'nullable',
        'rarity_id'           => 'required|exists:rarities,id',
        'name'                => 'required|unique:features|between:3,100',
        'description'         => 'nullable',
        'image'               => 'mimes:png',
        'mut_level'           => 'nullable',
        'mut_type'            => 'nullable',
    ];

    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'feature_category_id' => 'nullable',
        'species_id'          => 'nullable',
        'subtype_id'          => 'nullable',
        'rarity_id'           => 'required|exists:rarities,id',
        'name'                => 'required|between:3,100',
        'description'         => 'nullable',
        'image'               => 'mimes:png',
        'mut_level'           => 'nullable',
        'mut_type'            => 'nullable',
    ];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the rarity of this feature.
     */
    public function rarity() {
        return $this->belongsTo(Rarity::class);
    }

    /**
     * Get the species the feature belongs to.
     */
    public function species() {
        return $this->belongsTo(Species::class);
    }

    /**
     * Get the subtype the feature belongs to.
     */
    public function subtype() {
        return $this->belongsTo(Subtype::class);
    }

    /**
     * Get the category the feature belongs to.
     */
    public function category() {
        return $this->belongsTo(FeatureCategory::class, 'feature_category_id');
    }

    /**
     * Get the example images.
     */
    public function exampleImages() {
        return $this->hasMany('App\Models\Feature\FeatureExample', 'feature_id')->orderBy('sort', 'DESC');
    }

    /**
     * Get the FIRST example image (if only 1).
     */
    public function singleExample() {
        return $this->hasOne('App\Models\Feature\FeatureExample', 'feature_id');
    }

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to sort features in alphabetical order.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param bool                                  $reverse
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortAlphabetical($query, $reverse = false) {
        return $query->orderBy('name', $reverse ? 'DESC' : 'ASC');
    }

    /**
     * Scope a query to sort features in category order.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortCategory($query) {
        if (FeatureCategory::all()->count()) {
            return $query->orderBy(FeatureCategory::select('sort')->whereColumn('features.feature_category_id', 'feature_categories.id'), 'DESC');
        }

        return $query;
    }

    /**
     * Scope a query to sort features in species order.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortSpecies($query) {
        $ids = Species::orderBy('sort', 'DESC')->pluck('id')->toArray();

        return count($ids) ? $query->orderBy(DB::raw('FIELD(species_id, '.implode(',', $ids).')')) : $query;
    }

    /**
     * Scope a query to sort features in subtype order.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortSubtype($query) {
        $ids = Subtype::orderBy('sort', 'DESC')->pluck('id')->toArray();

        return count($ids) ? $query->orderBy(DB::raw('FIELD(subtype_id, '.implode(',', $ids).')')) : $query;
    }

    /**
     * Scope a query to sort features in rarity order.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param bool                                  $reverse
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortRarity($query, $reverse = false) {
        $ids = Rarity::orderBy('sort', $reverse ? 'ASC' : 'DESC')->pluck('id')->toArray();

        return count($ids) ? $query->orderBy(DB::raw('FIELD(rarity_id, '.implode(',', $ids).')')) : $query;
    }

    /**
     * Scope a query to sort features by newest first.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortNewest($query) {
        return $query->orderBy('id', 'DESC');
    }

    /**
     * Scope a query to sort features oldest first.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortOldest($query) {
        return $query->orderBy('id');
    }

    /**
     * Scope a query to show only visible features.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed|null                            $user
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible($query, $user = null) {
        if ($user && $user->hasPower('edit_data')) {
            return $query;
        }

        return $query->where('is_visible', 1);
    }

    /**
     * Scope a query to show only features locked or unlocked.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed                                 $locked
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLocked($query, $locked = true) {
        return $query->where('is_locked', $locked);
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Displays the model's name, linked to its encyclopedia page.
     *
     * @return string
     */
    public function getDisplayNameAttribute() {
        return ($this->code_id ? $this->code_id.' - ' : null).'<a href="'.$this->url.'" class="display-trait">'.$this->name.'</a>'.($this->rarity ? ' ('.$this->rarity->displayName.')' : '');
    }

    /**
     * Displays the name with code.
     *
     * @return string
     */
    public function getCodeNameAttribute() {
        return ($this->code_id ? $this->code_id.' - ' : null).$this->name;
    }

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute() {
        return 'images/data/traits';
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getImageFileNameAttribute() {
        return $this->id.'-'.$this->hash.'-image.png';
    }

    /**
     * Gets the path to the file directory containing the model's image.
     *
     * @return string
     */
    public function getImagePathAttribute() {
        return public_path($this->imageDirectory);
    }

    /**
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getImageUrlAttribute() {
        if (!$this->has_image) {
            return null;
        }

        return asset($this->imageDirectory.'/'.$this->imageFileName);
    }

    /**
     * Gets the file name of the model's example image.
     *
     * @return string
     */
    public function getExampleImageFileNameAttribute() {
        return $this->example_hash.$this->id.'-example-image.png';
    }

    /**
     * Gets the URL of the model's example image.
     *
     * @return string
     */
    public function getExampleImageUrlAttribute() {
        if (!$this->has_example_image) {
            return null;
        }

        return asset($this->imageDirectory.'/'.$this->exampleImageFileName);
    }

    /**
     * Gets the URL of the model's encyclopedia page.
     *
     * @return string
     */
    public function getUrlAttribute() {
        return url('world/traits?name='.$this->name);
    }

    /**
     * Gets the URL for a masterlist search of characters in this category.
     *
     * @return string
     */
    public function getSearchUrlAttribute() {
        return url('masterlist?feature_ids[]='.$this->id);
    }

    /**
     * Gets the admin edit URL.
     *
     * @return string
     */
    public function getAdminUrlAttribute() {
        return url('admin/data/traits/edit/'.$this->id);
    }

    /**
     * Gets the power required to edit this model.
     *
     * @return string
     */
    public function getAdminPowerAttribute() {
        return 'edit_data';
    }

    /**
     * Gets the mutation level of the feature.
     *
     * @return string
     */
    public function getLevelAttribute() {
        switch ($this->mut_level) {
            case 1:
                return 'Minor';
                break;
            case 2:
                return 'Major';
                break;
        }

        return '<i>Unknown</i>';
    }

    /**
     * Gets the mutation type of the feature.
     *
     * @return string
     */
    public function getTypeAttribute() {
        switch ($this->mut_type) {
            case 1:
                return 'Breed Only';
                break;
            case 2:
                return 'Custom Requestable';
                break;
        }

        return '<i>Undefined</i>';
    }

    /**********************************************************************************************

        Other Functions

    **********************************************************************************************/

    public static function getDropdownItems($withHidden = 0) {
        $visibleOnly = 1;
        if ($withHidden) {
            $visibleOnly = 0;
        }

        if (config('lorekeeper.extensions.organised_traits_dropdown')) {
            $sorted_feature_categories = collect(FeatureCategory::all()->where('is_visible', '>=', $visibleOnly)->sortBy('sort')->pluck('name')->toArray());

            $grouped = self::where('is_visible', '>=', $visibleOnly)->select('name', 'id', 'feature_category_id', 'code_id')->with('category')->orderBy('name')->get()->sortBy('code_id', SORT_NATURAL)->keyBy('id')->groupBy('category.name', $preserveKeys = true)->toArray();
            if (isset($grouped[''])) {
                if (!$sorted_feature_categories->contains('Miscellaneous')) {
                    $sorted_feature_categories->push('Miscellaneous');
                }
                $grouped['Miscellaneous'] ??= [] + $grouped[''];
            }

            $sorted_feature_categories = $sorted_feature_categories->filter(function ($value, $key) use ($grouped) {
                return in_array($value, array_keys($grouped), true);
            });
            foreach ($grouped as $category => $features) {
                foreach ($features as $id  => $feature) {
                    $grouped[$category][$id] = (isset($feature['code_id']) && $feature['code_id'] ? $feature['code_id'].' - ' : '').$feature['name'];
                }
            }
            $features_by_category = $sorted_feature_categories->map(function ($category) use ($grouped) {
                return [$category => $grouped[$category]];
            });

            return $features_by_category;
        } else {
            return self::where('is_visible', '>=', $visibleOnly)->orderBy('name')->pluck('codeName', 'id')->toArray();
        }
    }
}
