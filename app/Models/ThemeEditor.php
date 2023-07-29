<?php

namespace App\Models;

use Config;
use App\Models\Model;

use App\Traits\Commentable;

class ThemeEditor extends Model
{
    use Commentable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'nav_color', 'nav_text_color', 'header_image_display', 'header_image_url', 'background_color', 'background_image_url', 'background_size', 
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'theme_editor';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = false;
    
    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [

    ];
    
    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [

    ];

}
