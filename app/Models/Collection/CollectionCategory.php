<?php

namespace App\Models\Collection;

use Config;
use App\Models\Model;

class CollectionCategory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'sort', 'has_image', 'description' 
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'collection_categories';
    
    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'name' => 'required|unique:collection_categories|between:3,25',
        'description' => 'nullable',
        'image' => 'mimes:png',
    ];
    
    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name' => 'required|between:3,25',
        'description' => 'nullable',
        'image' => 'mimes:png',
    ];

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
        return '<a href="'.$this->url.'" class="display-category">'.$this->name.'</a>';
    }

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute()
    {
        return 'images/data/collection-categories';
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getCategoryImageFileNameAttribute()
    {
        return $this->id . '-image.png';
    }

    /**
     * Gets the path to the file directory containing the model's image.
     *
     * @return string
     */
    public function getCategoryImagePathAttribute()
    {
        return public_path($this->imageDirectory);
    }
    
    /**
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getCategoryImageUrlAttribute()
    {
        if (!$this->has_image) return null;
        return asset($this->imageDirectory . '/' . $this->categoryImageFileName);
    }

    /**
     * Gets the URL of the model's encyclopedia page.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('world/collection-categories?name='.$this->name);
    }

    /**
     * Gets the URL for an encyclopedia search of collections in this category.
     *
     * @return string
     */
    public function getSearchUrlAttribute()
    {
        return url('world/collections?collection_category_id='.$this->id);
    }

        /**
     * Gets the admin edit URL.
     *
     * @return string
     */
    public function getAdminUrlAttribute()
    {
        return url('admin/data/collections/collection-categories/edit/'.$this->id);
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
