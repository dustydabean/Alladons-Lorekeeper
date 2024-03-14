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
        'name', 'theme_id', 'title_color', 'nav_color', 'nav_text_color', 'header_image_display', 'header_image_url', 'background_color', 'background_image_url', 'background_size', 
        'main_color', 'main_text_color', 'card_color', 'card_header_color', 'card_header_text_color', 'card_text_color', 'link_color', 'primary_button_color', 'secondary_button_color', 'is_released'
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
        'name' => ['required'],
        'header_image_url' => ['nullable', 'regex:/(https?):\/\/(www\.)?[\w.-]+\.[a-zA-Z]+\/((([\w\/-]+)\/)?[\w.-]+\.(png|gif|jpe?g)$)/'],
        'background_image_url' => ['nullable', 'regex:/(https?):\/\/(www\.)?[\w.-]+\.[a-zA-Z]+\/((([\w\/-]+)\/)?[\w.-]+\.(png|gif|jpe?g)$)/']
    ];
    
    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name' => ['required'],
        'header_image_url' => ['nullable', 'regex:/(https?):\/\/(www\.)?[\w.-]+\.[a-zA-Z]+\/((([\w\/-]+)\/)?[\w.-]+\.(png|gif|jpe?g)$)/'],
        'background_image_url' => ['nullable', 'regex:/(https?):\/\/(www\.)?[\w.-]+\.[a-zA-Z]+\/((([\w\/-]+)\/)?[\w.-]+\.(png|gif|jpe?g)$)/']
    ];

    public function scopeReleased($query)
    {
        return $query->where('is_released', 1);
    }

}
