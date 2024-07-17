<?php

namespace App\Models\Collection;

use Config;
use DB;
use App\Models\Model;

class Collection extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'has_image','description', 'parsed_description', 'reference_url', 'artist_alias' ,'artist_url', 'is_visible', 'collection_category_id', 'parent_id'
    ];

    protected $appends = ['image_url'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'collections';

    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'name' => 'required|unique:collections',
        'description' => 'nullable',
        'image' => 'mimes:png',
        'collection_category_id' => 'nullable',
    ];

    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name' => 'required',
        'description' => 'nullable',
        'image' => 'mimes:png',
        'collection_category_id' => 'nullable',
    ];

    /**********************************************************************************************
        RELATIONS
    **********************************************************************************************/

    /**
     * Get the collection's items.
     */
    public function ingredients()
    {
        return $this->hasMany('App\Models\Collection\CollectionIngredient');
    }

    /**
     * Get the users who have this collection.
     */
    public function users()
    {
        return $this->belongsToMany('App\Models\User\User', 'user_collections')->withPivot('id');
    }

    /**
     * Get the category the collection belongs to.
     */
    public function category()
    {
        return $this->belongsTo('App\Models\Collection\CollectionCategory', 'collection_category_id');
    }

      /**
     * Get the prompts parent
     */
    public function parent()
    {
        return $this->belongsTo('App\Models\Collection\Collection', 'parent_id');
    }

    /**
     * Get the prompts children
     */
    public function children()
    {
        return $this->hasMany('App\Models\Collection\Collection', 'parent_id');
    }


    /**********************************************************************************************
        SCOPES
    **********************************************************************************************/

    /**
     * Scope a query to sort items in alphabetical order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  bool                                   $reverse
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortAlphabetical($query, $reverse = false)
    {
        return $query->orderBy('name', $reverse ? 'DESC' : 'ASC');
    }

    /**
     * Scope a query to sort items by newest first.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortNewest($query)
    {
        return $query->orderBy('id', 'DESC');
    }

    /**
     * Scope a query to sort features oldest first.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortOldest($query)
    {
        return $query->orderBy('id');
    }

        /**
     * Scope a query to sort gears in category order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortCategory($query)
    {
        $ids = CollectionCategory::orderBy('sort', 'DESC')->pluck('id')->toArray();
        return count($ids) ? $query->orderByRaw(DB::raw('FIELD(collection_category_id, '.implode(',', $ids).')')) : $query;
    }

    /**
     * Scope a query to show only visible collections.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible($query, $withHidden = 0)
    {
        if($withHidden) return $query;
        return $query->where('is_visible', 1);
    }


    /**********************************************************************************************
        ACCESSORS
    **********************************************************************************************/

    /**
     * Gets the decoded output json
     *
     * @return array
     */
    public function getRewardsAttribute()
    {
        $rewards = [];
        if($this->output) {
            $assets = $this->getRewardItemsAttribute();

            foreach($assets as $type => $a)
            {
                $class = getAssetModelString($type, false);
                foreach($a as $id => $asset)
                {
                    $rewards[] = (object)[
                        'rewardable_type' => $class,
                        'rewardable_id' => $id,
                        'quantity' => $asset['quantity']
                    ];
                }
            }
        }
        return $rewards;
    }

    /**
     * Interprets the json output and retrieves the corresponding items
     *
     * @return array
     */
    public function getRewardItemsAttribute()
    {
        return parseAssetData(json_decode($this->output, true));
    }

    /**
     * Gets the URL of the individual collection's page, by ID.
     *
     * @return string
     */
    public function getIdUrlAttribute()
    {
        return url('world/collections/'.$this->id);
    }

    /**
     * Displays the model's name, linked to its encyclopedia page.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return '<a href="'.$this->idUrl.'" class="display-item">'.$this->name.'</a>';
    }

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute()
    {
        return 'images/data/collections';
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getImageFileNameAttribute()
    {
        return $this->id . '-image.png';
    }

    /**
     * Gets the path to the file directory containing the model's image.
     *
     * @return string
     */
    public function getImagePathAttribute()
    {
        return public_path($this->imageDirectory);
    }

    /**
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        if (!$this->has_image) return null;
        return asset($this->imageDirectory . '/' . $this->imageFileName);
    }

    /**
     * Gets the URL of the model's encyclopedia page.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('world/collections?name='.$this->name);
    }

    /**
     * Gets the currency's asset type for asset management.
     *
     * @return string
     */
    public function getAssetTypeAttribute()
    {
        return 'collections';
    }

    /**
    * Gets the currency's asset type for asset management.
    *
    * @return bool
    */
   public function getLockedAttribute()
   {
       return $this->needs_unlocking && !User;
   }

   /**
    * Returns whether or not a collection's ingredients are all currency
    *
    * @return bool
    */
   public function getOnlyCurrencyAttribute()
   {
        if(count($this->ingredients))
        {
            $type = [];
            foreach($this->ingredients as $ingredient)
            {
                $type[] = $ingredient->ingredient_type;
            }
            $types = array_flip($type);
            if(count($types) == 1 && key($types) == 'Currency') return true;
            else return false;
        }
        else return false;
   }

   /**
     * Gets the admin edit URL.
     *
     * @return string
     */
    public function getAdminUrlAttribute()
    {
        return url('admin/data/collections/edit/'.$this->id);
    }

    /**
     * Gets the power required to edit this model.
     *
     * @return string
     */
    public function getAdminPowerAttribute()
    {
        return 'edit_data';
    }
}