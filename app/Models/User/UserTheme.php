<?php

namespace App\Models\User;

use App\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserTheme extends Model {
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'theme_id', 'user_id'
  ];

  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'user_themes';

  /**********************************************************************************************
    
        RELATIONS
   **********************************************************************************************/

  /**
   * Get the user who owns the theme.
   */
  public function user() {
    return $this->belongsTo('App\Models\User\User');
  }

  /**
   * Get the theme associated with this user.
   */
  public function theme() {
    return $this->belongsTo('App\Models\Theme');
  }

  /**********************************************************************************************
    
        ACCESSORS
   **********************************************************************************************/

  /**
   * Gets the stack's asset type for asset management.
   *
   * @return string
   */
  public function getAssetTypeAttribute() {
    return 'user_theme';
  }
}
