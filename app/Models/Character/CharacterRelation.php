<?php

namespace App\Models\Character;

use App\Models\Model;

class CharacterRelation extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'character_1_id', 'character_2_id', 'info', 'type', 'status',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'character_relations';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'info' => 'array',
    ];

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
     * Get first character.
     */
    public function characterOne() {
        return $this->belongsTo(Character::class, 'character_1_id');
    }

    /**
     * Get second character.
     */
    public function characterTwo() {
        return $this->belongsTo(Character::class, 'character_2_id');
    }

    /**********************************************************************************************

        OTHER FUNCTIONS

    **********************************************************************************************/

    /**
     * Returns the other character in the relation based on the given character id.
     *
     * @param mixed $id
     */
    public function getOtherCharacter($id) {
        return $this->character_1_id == $id ? $this->characterTwo : $this->characterOne;
    }

    /**
     * Gets the information for the relation based on the given character id.
     *
     * @param mixed $id
     */
    public function getRelationshipInfo($id) {
        return $this->info ? $this->info[$id == $this->character_1_id ? 0 : 1] : null;
    }

    /**
     * Get the character in the relation belonging to the given user id, if any.
     *
     * @param mixed $id
     */
    public function getCharacterForUser($id) {
        return $this->characterOne->user_id == $id ? $this->characterOne : ($this->characterTwo->user_id == $id ? $this->characterTwo : null);
    }
}
