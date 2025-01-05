<?php

namespace App\Models\Character;

use Config;
use Settings;
use App\Models\Model;
use App\Models\User\User;

class CharacterFolder extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'user_id'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'character_folders';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the user this folder belongs to
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all characters in this folder
     */
    public function characters()
    {
        return $this->hasMany(Character::class, 'folder_id');
    }

    /**********************************************************************************************

        ATTRIBUTES

    **********************************************************************************************/

    /**
     * returns the folder url
     */
    public function getUrlAttribute()
    {
        return url('user/'.$this->user->name.'/characters/' . $this->name);
    }
}