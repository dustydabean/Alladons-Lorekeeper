<?php

namespace App\Models;

use Config;
use App\Models\Model;

class Activity extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'sort', 'has_image', 'description', 'parsed_description', 'is_active', 'module', 'data'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'activities';

    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'name' => 'required|between:3,100',
        'description' => 'nullable',
        'image' => 'mimes:jpeg,jpg,gif,png',
    ];

    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name' => 'required|between:3,100',
        'description' => 'nullable',
        'image' => 'mimes:jpeg,jpg,gif,png',
    ];

    /**********************************************************************************************
    
        ACCESSORS

     **********************************************************************************************/

    /**
     * Displays the shop's name, linked to its purchase page.
     *
     * @return string
     */
    public function getDisplayNameAttribute() {
        return '<a href="' . $this->url . '">' . $this->name . '</a>';
    }

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute() {
        return 'images/data/activities';
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getImageFileNameAttribute() {
        return $this->id . '-image.png';
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
        if (!$this->has_image) return null;
        return asset($this->imageDirectory . '/' . $this->ImageFileName);
    }

    /**
     * Gets the URL of the model's encyclopedia page.
     *
     * @return string
     */
    public function getUrlAttribute() {
        return url('activities/' . $this->id);
    }

    /**
     * Get the data attribute as an associative array.
     *
     * @return array
     */
    public function getDataAttribute($data) {
        return $this->service->getData($data);
    }

    /**
     * Get the service associated with the associated module.
     *
     * @return mixed
     */
    public function getServiceAttribute() {
        $class = 'App\Services\Activity\\' . str_replace(' ', '', ucwords(str_replace('_', ' ', $this->module))) . 'Service';
        return (new $class());
    }
}
