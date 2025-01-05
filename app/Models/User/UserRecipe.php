<?php

namespace App\Models\User;

use App\Models\Model;

class UserRecipe extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'recipe_id', 'user_id',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_recipes';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;

    /**********************************************************************************************

        RELATIONS
    **********************************************************************************************/

    /**
     * Get the user who owns the recipe.
     */
    public function user() {
        return $this->belongsTo('App\Models\User\User');
    }

    /**
     * Get the recipe associated with this user.
     */
    public function recipe() {
        return $this->belongsTo('App\Models\Recipe\Recipe');
    }

    /**********************************************************************************************

        ACCESSORS
    **********************************************************************************************/

    /**
     * Gets the stack's asset type for asset management.
     *
     * @return string
     */
    public function getAssetTypeAttribute() {
        return 'user_recipe';
    }
}
