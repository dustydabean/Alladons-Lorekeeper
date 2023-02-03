<?php

namespace App\Models\Character;

use Config;
use Settings;
use App\Models\Model;

class CharacterTransfer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'character_id', 'sender_id', 'user_reason', 'recipient_id',
        'status', 'is_approved', 'reason', 'data'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'character_transfers';

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
     * Get the user who initiated the transfer.
     */
    public function sender()
    {
        return $this->belongsTo('App\Models\User\User', 'sender_id');
    }

    /**
     * Get the user who received the transfer.
     */
    public function recipient()
    {
        return $this->belongsTo('App\Models\User\User', 'recipient_id');
    }

    /**
     * Get the character to be transferred.
     */
    public function character()
    {
        return $this->belongsTo('App\Models\Character\Character');
    }

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to only include pending trades, as well as trades pending staff approval.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        $query->where('status', 'Pending');

        if(Settings::get('open_transfers_queue')) {
            $query->orWhere(function($query) {
                $query->where('status', 'Accepted')->where('is_approved', 0);
            });
        }

        return $query;
    }

    /**
     * Scope a query to only include completed trades.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        $query->where('status', 'Rejected')->orWhere('status', 'Canceled')->orWhere(function($query) {
            $query->where('status', 'Accepted')->where('is_approved', 1);
        });;
        return $query;
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Check if the transfer is active.
     *
     * @return bool
     */
    public function getIsActiveAttribute()
    {
        if($this->status == 'Pending') return true;
        if(($this->status == 'Accepted') && $this->is_approved == 0) return true;

        return false;
    }

    /**
     * Get the data attribute as an associative array.
     *
     * @return array
     */
    public function getDataAttribute()
    {
        return json_decode($this->attributes['data'], true);
    }
}
