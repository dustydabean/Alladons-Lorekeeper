<?php

namespace App\Models;

use App\Facades\Settings;
use App\Models\Model;
use App\Models\User\UserSettings;

class EventTeam extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'has_image', 'score',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'event_teams';

    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $validationRules = [
        'name.*' => 'required|between:3,100',
        'image.*' => 'nullable|mimes:png,jpeg,jpg',
    ];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get members of this team.
     */
    public function members()
    {
        return $this->hasMany(UserSettings::class, 'team_id');
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Displays the model's name.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return '<strong>'.$this->name.'</strong>';
    }

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute()
    {
        return 'images/events/teams';
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
        return asset($this->imageDirectory . '/' . $this->ImageFileName);
    }

    /**
     * Displays the team's score, weighted.
     *
     * @return int
     */
    public function getWeightedScoreAttribute()
    {
        if(Settings::get('event_weighting') && $this->members->count()) {
            return $this->score / $this->members->count();
        }
        return $this->score;
    }
}
