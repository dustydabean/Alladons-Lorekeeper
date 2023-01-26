<?php

namespace App\Models\Collection;

use App;
use Config;
use DB;
use App\Models\Model;

class CollectionIngredient extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'collection_id', 'ingredient_type', 'ingredient_data', 'quantity'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'collection_ingredients';

    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'collection_id' => 'required',
        'ingredient_type' => 'required',
        'ingredient_data' => 'required',
        'quantity' => 'required|integer|min:1',
    ];

    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'collection_id' => 'required',
        'ingredient_type' => 'required',
        'ingredient_data' => 'required',
        'quantity' => 'required|integer|min:1',
    ];

    /**********************************************************************************************
    
        RELATIONS
    **********************************************************************************************/

    /**
     * Get the associated collection.
     */
    public function collection() 
    {
        return $this->belongsTo('App\Models\Collection\Collection');
    }

    /**********************************************************************************************
    
        ACCESSORS
    **********************************************************************************************/

    /**
     * Gets the json decoded data array.
     *
     * @return string
     */
    public function getDataAttribute()
    {
        return json_decode($this->ingredient_data);
    }

    /**
     * Gets the associated ingredient ingredient(s) or category(ies).
     *
     * @return string
     */
    public function getIngredientAttribute()
    {
        switch ($this->ingredient_type)
        {
            case 'Item':
                return App\Models\Item\Item::where('id', $this->data[0])->get()[0];
        }
        return null;
    }
}