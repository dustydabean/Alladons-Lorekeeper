<?php

namespace App\Models\User;

use App\Models\Model;
use App\Models\User\User;

class StaffProfile extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'contacts', 'text'
    ];

    /**
     * The primary key of the model.
     *
     * @var string
     */
    public $primaryKey = 'user_id';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'staff_profiles';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = false;

    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'contacts' => 'nullable',
        'text' => 'nullable|between:3,250',
    ];
    
    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'contacts' => 'nullable',
        'text' => 'nullable|between:3,250',
    ];

    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/
    
    /**
     * Get the user this set of settings belongs to.
     */
    public function user() 
    {
        return $this->belongsTo('App\Models\User\User');
    }

    /**
     * Get the contacts attribute as an associative array.
     *
     * @return array
     */
    public function getContactsAttribute()
    {
        return json_decode($this->attributes['contacts'], true);
    }
}