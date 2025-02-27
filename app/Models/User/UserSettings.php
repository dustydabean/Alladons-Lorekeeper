<?php

namespace App\Models\User;

use App\Models\EventTeam;
use App\Models\Model;

class UserSettings extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'is_fto', 'submission_count', 'banned_at', 'ban_reason', 'birthday_setting', 'team_id',
        'deactivate_reason', 'deactivated_at', 'dev_log_notif', 'content_warning_visibility',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_settings';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'banned_at'      => 'datetime',
        'deactivated_at' => 'datetime',
    ];

    /**
     * The primary key of the model.
     *
     * @var string
     */
    public $primaryKey = 'user_id';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the user this set of settings belongs to.
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the team this set of settings belongs to.
     */
    public function team() {
        return $this->belongsTo(EventTeam::class, 'team_id');
    }
}
