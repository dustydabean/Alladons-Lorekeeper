<?php

namespace App\Models\Character;

use App\Models\User\User;
use App\Models\Model;

class CharacterBreedingSlot extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'character_id', 'offspring_id', 'user_id', 'user_url',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'character_breeding_slots';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the character associated with this record.
     */
    public function character() {
        return $this->belongsTo(Character::class, 'character_id');
    }

    /**
     * Get the offspring associated with this record.
     */
    public function offspring() {
        return $this->belongsTo(Character::class, 'offspring_id');
    }

    /**
     * Get the user associated with this record.
     */
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Displays a link using the user's URL.
     *
     * @return string
     */
    public function displayLink() {
        if ($this->user_id) {
            return $this->user->displayName;
        } elseif ($this->user_url) {
            $userEntry = checkAlias($this->user_url, false);
            if (is_object($userEntry)) {
                $this->user_id = $userEntry->id;
                $this->user_url = null;
                $this->save();
            } else {
                return prettyProfileLink($this->user_url);
            }
        }

        return '<span style="opacity: 0.75;">No User Assigned</span>';
    }

    /**
     * Displays a link using the user's URL.
     *
     * @return string
     */
    public function displayOffspring() {
        if ($this->offspring_id) {
            return $this->offspring->displayName;
        }

        return '<span style="opacity: 0.75;">No Offspring Assigned</span>';
    }
}
