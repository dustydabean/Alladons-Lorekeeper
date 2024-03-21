<?php

namespace App\Models\Character;

use Auth;

use App\Models\Model;

use App\Models\Character\Character;

class CharacterLineage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'character_id', 'parent_1_id', 'parent_1__name', 'parent_2_id', 'parent_2_name',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'character_lineages';

    /**
     * gets the character
     */
    public function character()
    {
        return $this->belongsTo(Character::class);
    }

    /**
     * Gets the parent_1 of this character
     */
    public function parent_1()
    {
        return $this->hasOne(Character::class, 'id', 'parent_1_id');
    }

    /**
     * Gets the parent_2 of this character
     */
    public function parent_2()
    {
        return $this->hasOne(Character::class, 'id', 'parent_2_id');
    }

    /**
     * Gets the display URL and/or name of an ancestor, or "Unknown" if there is none
     * @param   string  $ancestor
     * @return  string
     */
    public function getDisplayName($ancestor)
    {
        if(isset($this[$ancestor.'_id']) && $this[$ancestor])
            return $this[$ancestor]->getDisplayNameAttribute();

        if(isset($this[$ancestor.'_name']) && $this[$ancestor.'_name'])
            return $this[$ancestor.'_name'];

        return "Unknown";
    }

    /**
     * Returns both parents of this character as an array
     */
    public function getParentsAttribute()
    {
        return [
            $this->parent_1, $this->parent_2,
        ];
    }
}
