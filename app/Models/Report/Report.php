<?php

namespace App\Models\Report;

use Config;
use DB;
use Carbon\Carbon;
use App\Models\Model;
use App\Traits\Commentable;

class Report extends Model
{
    use Commentable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'staff_id', 'url',
        'comments', 'staff_comments', 'parsed_staff_comments',
        'status', 'data', 'error_type', 'is_br'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'reports';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;
    
    /**
     * Validation rules for report creation.
     *
     * @var array
     */
    public static $createRules = [
        'url' => 'required',
    ];
    
    /**
     * Validation rules for report updating.
     *
     * @var array
     */
    public static $updateRules = [
        'url' => 'required',
    ];

    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/
    /**
     * Get the user who made the report.
     */
    public function user() 
    {
        return $this->belongsTo('App\Models\User\User', 'user_id');
    }
    
    /**
     * Get the staff who processed the report.
     */
    public function staff() 
    {
        return $this->belongsTo('App\Models\User\User', 'staff_id');
    }

    /**********************************************************************************************
    
        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to only include pending reports.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Pending');
    }

    /**
     * Scope a query to only include reports assigned to a given user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAssignedToMe($query, $user)
    {
        return $query->where('status', 'Assigned')->where('staff_id', $user->id);
    }

    /**
     * Scope a query to only include viewable reports.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeViewable($query, $user)
    {
        if($user && $user->hasPower('manage_reports')) return $query;
        return $query->where(function($query) use ($user) {
            if($user) $query->where('user_id', $user->id)->orWhere('error_type', '!=', 'exploit');
            else $query->where('error_type', '!=', 'exploit');
        });
    }

    /**
     * Scope a query to sort reports oldest first.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortOldest($query)
    {
        return $query->orderBy('id');
    }

    /**********************************************************************************************
    
        ACCESSORS

    **********************************************************************************************/

    /**
     * Get the data attribute as an associative array.
     *
     * @return array
     */
    public function getDataAttribute()
    {
        return json_decode($this->attributes['data'], true);
    }

    /**
     * Get the viewing URL of the report/claim.
     *
     * @return string
     */
    public function getViewUrlAttribute()
    {
        return url('reports/view/'.$this->id);
    }

    /**
     * Get the admin URL (for processing purposes) of the submission/claim.
     *
     * @return string
     */
    public function getAdminUrlAttribute()
    {
        return url('admin/reports/edit/'.$this->id);
    }

    /**
     * Displays the news post title, linked to the news post itself.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return '<a href="'.$this->viewurl.'">'.'Report #-' . $this->id.'</a>';
    }

}
