<?php

namespace App\Models\Character;

use App\Models\Model;

class CharacterPedigree extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'parsed_description',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'character_pedigrees';
    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'name' => 'required|unique:character_pedigrees',
        'description'  => 'nullable',
    ];

    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name' => 'required',
        'description'  => 'nullable',
    ];
}
