<?php

namespace App\Models\User;

use App\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCollection extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'collection_id', 'user_id'
    ];

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_collections';

    /**********************************************************************************************
    
        RELATIONS
    **********************************************************************************************/

    /**
     * Get the user who owns the collection.
     */
    public function user() 
    {
        return $this->belongsTo('App\Models\User\User');
    }

    /**
     * Get the collection associated with this user.
     */
    public function collection() 
    {
        return $this->belongsTo('App\Models\Collection\Collection');
    }

    /**********************************************************************************************
    
        ACCESSORS
    **********************************************************************************************/

    /**
     * Gets the stack's asset type for asset management.
     *
     * @return string
     */
    public function getAssetTypeAttribute()
    {
        return 'user_collection';
    }
}