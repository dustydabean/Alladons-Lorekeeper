<?php

namespace App\Models\Pet;

use Config;
use DB;
use Carbon\Carbon;
use Notifications;
use App\Models\Model;

use App\Models\User\User;
use App\Models\User\UserPet;
use App\Models\Pet\Pet;
use App\Models\Pet\PetDropData;
use App\Models\Item\Item;
use App\Models\Item\ItemLog;

class PetDrop extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'drop_id', 'user_pet_id', 'parameters', 'drops_available', 'next_day'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pet_drops';

    /**
     * Dates on the model to convert to Carbon instances.
     *
     * @var array
     */
    public $dates = ['next_day'];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the associated user pet.
     */
    public function user_pet()
    {
        return $this->belongsTo('App\Models\User\UserPet', 'user_pet_id');
    }

    /**
     * Get the category the user pet belongs to.
     */
    public function dropData()
    {
        return $this->belongsTo('App\Models\Pet\PetDropData', 'drop_id');
    }

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to only include drops that require updating.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRequiresUpdate($query)
    {
        return $query->whereNotIn('user_pet_id', UserPet::pluck('pet_id')->toArray())->whereIn('drop_id', PetDropData::where('is_active', 1)->pluck('id')->toArray())->where('next_day', '<', Carbon::now());
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Get the item(s) a given user pet should be dropping.
     *
     */
    public function getPetItemAttribute()
    {
        // get rewards from drop data
        return $this->dropData->rewards(true)[strtolower($this->parameters)];
    }

    /**
     * Get the item(s) a given user pet should be dropping.
     *
     */
    public function getVariantItemAttribute()
    {
        // get rewards from drop data
        if (!$this->user_pet->variant || !isset($this->dropData->rewards(false)[str_replace(' ', '_', $this->user_pet->variant->name)])) return null;
        return $this->dropData->rewards(false)[strtolower(str_replace(' ', '_', $this->user_pet->variant->name))];
    }

    /**
     * Get the display of the group a user pet belongs to, so long as the species has more than one.
     *
     */
    public function getGroupAttribute()
    {
        if(count($this->dropData->parameters) > 1) return ' ('.$this->parameters.')';
        else return null;
    }

    /**********************************************************************************************

        OTHER FUNCTIONS

    **********************************************************************************************/

    /**
     * Create drop info for a user pet.
     *
     * @param int              $id
     */
    public function createDrop($id, $parameters = null)
    {
        $user_pet = UserPet::find($id);
        $dropData = $user_pet->pet->dropData;
        $drop = $this->create([
            'drop_id' => $dropData->id,
            'user_pet_id' => $id,
            'parameters' => $parameters ? $parameters : $dropData->rollParameters(),
            'drops_available' => 0,
            'next_day' => Carbon::now()->add($dropData->data['frequency']['frequency'], $dropData->data['frequency']['interval'])->startOf($dropData->data['frequency']['interval']),
        ]);
    }
}
