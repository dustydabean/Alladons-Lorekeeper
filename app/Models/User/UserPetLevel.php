<?php

namespace App\Models\User;

use App\Models\Model;
use App\Models\Pet\PetLevel;
use Carbon\Carbon;

class UserPetLevel extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_pet_id', 'bonding_level', 'bonding', 'next_level_at',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_pet_levels';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'next_level_at' => 'datetime',
    ];
    
    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the pet associated with this pet stack.
     */
    public function pet() {
        return $this->belongsTo(UserPet::class, 'user_pet_id');
    }

    /**
     * Get the level associated with this pet stack.
     */
    public function level() {
        return $this->belongsTo(PetLevel::class, 'bonding_level');
    }

    /**********************************************************************************************

        ATTRIBUTES

    **********************************************************************************************/

    /**
     * Returns the level name of the pet.
     */
    public function getLevelNameAttribute() {
        return $this->bonding_level ?? 1;
    }

    /**
     * Gets the next level date for the pet, defaulting to a year from now 
     * for anything that doesn't have a proper next_level_at value.
     */
    public function getNextLevelAttribute() {
        return $this->next_level_at ?? Carbon::now()->addYear()->startOfDay();
    }
    
    /**
     * Get when the next level will be reached.
     */
    public function getLevelsAtAttribute() {
        $nextLevelDate = $this->nextLevel;
        if (isset($this->bonding) && ($this->bonding > 0)) {
            $days = ($this->bonding * 7);
            $nextLevelDate = $nextLevelDate->subDays($days ?? 0);
        }

        return $nextLevelDate;
    }
}
