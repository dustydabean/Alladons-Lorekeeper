<?php

namespace App\Models\Theme;

use Config;
use DB;
use App\Models\Model;
use App\Models\Theme\ThemeCategory;

use App\Models\User\User;
use App\Models\Shop\Shop;
use App\Models\Prompt\Prompt;
use App\Models\User\UserTheme;

class Theme extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'hash', 'is_default', 'is_active', 'has_css', 'has_header', 'extension', 'creators'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'themes';

    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'name' => 'required|unique:themes|between:3,100',
        'image' => 'mimes:png,jpg,jpeg,gif',
        'css' => 'mimes:css',
        'active' => 'nullable|boolean',
        'default' => 'nullable|boolean',
        'creators_name' => 'required',
        'creators_url' => 'required',
    ];

    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name' => 'required|between:3,100',
        'image' => 'mimes:png,jpg,jpeg,gif',
        'css' => 'mimes:css',
        'active' => 'nullable|boolean',
        'default' => 'nullable|boolean',
        'creators_name' => 'required',
        'creators_url' => 'required',
    ];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the category the theme belongs to.
     */
    public function users()
    {
        return $this->hasMany('App\Models\User\User', 'theme_id');
    }

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to sort themes in alphabetical order.
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
     * Scope a query to sort themes by newest first.
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
     * Scope a query to show only released or "released" (at least one user-owned stack has ever existed) themes.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible($query)
    {
        return $query->where('is_active', 1);
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
        return $this->name;
    }

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute()
    {
        return 'themes';
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getImageFileNameAttribute()
    {
        return $this->id . '-image.'.$this->extension;
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
        if (!$this->has_header) return asset('images/header.png');
        return asset($this->imageDirectory . '/' . $this->imageFileName);
    }

    /**
     * Gets the number of users who have this
     *
     * @return string
     */
    public function getUserCountAttribute()
    {
        return User::where('is_banned',0)->where('theme_id',$this->id)->count();
    }

    /**********************************************************************************************

        OTHER FUNCTIONS

    **********************************************************************************************/




}
