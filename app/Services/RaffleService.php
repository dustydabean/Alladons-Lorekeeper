<?php namespace App\Services;

use DB;
use App\Notify;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Services\Service;
use App\Models\Raffle\RaffleGroup;
use App\Models\Raffle\Raffle;

class RaffleService  extends Service 
{
    /*
    |--------------------------------------------------------------------------
    | Raffle Service
    |--------------------------------------------------------------------------
    |
    | Handles creation and modification of raffles.
    |
    */

    /**
     * Creates a raffle.
     *
     * @param  array  $data
     * @return \App\Models\Raffle\Raffle
     */
    public function createRaffle($data)
    {
        DB::beginTransaction();
        if(!isset($data['is_active'])) $data['is_active'] = 0;
        $raffle = Raffle::create(Arr::only($data, ['name', 'is_active', 'winner_count', 'group_id', 'order']));
        DB::commit();
        return $raffle;
    }

    /**
     * Updates a raffle. 
     *
     * @param  array                     $data
     * @param  \App\Models\Raffle\Raffle $raffle
     * @return \App\Models\Raffle\Raffle
     */
    public function updateRaffle($data, $raffle) 
    {
        DB::beginTransaction();
        if(!isset($data['is_active'])) $data['is_active'] = 0;
        $raffle->update(Arr::only($data, ['name', 'is_active', 'winner_count', 'group_id', 'order']));
        DB::commit();
        return $raffle;
    }    

    /**
     * Deletes a raffle. 
     *
     * @param  \App\Models\Raffle\Raffle $raffle
     * @return bool
     */
    public function deleteRaffle($raffle) 
    {
        DB::beginTransaction();
        foreach($raffle->tickets as $ticket) $ticket->delete();
        $raffle->delete();
        DB::commit();
        return true;
    }   

    /**
     * Creates a raffle group.
     *
     * @param  array  $data
     * @return \App\Models\Raffle\RaffleGroup
     */
    public function createRaffleGroup($data)
    {
        DB::beginTransaction();
        if(!isset($data['is_active'])) $data['is_active'] = 0;
        $group = RaffleGroup::create(Arr::only($data, ['name', 'is_active']));
        DB::commit();
        return $group;
    }

    /**
     * Updates a raffle group. 
     *
     * @param  array                          $data
     * @param  \App\Models\Raffle\RaffleGroup $raffle
     * @return \App\Models\Raffle\Raffle
     */
    public function updateRaffleGroup($data, $group) 
    {
        DB::beginTransaction();
        if(!isset($data['is_active'])) $data['is_active'] = 0;
        $group->update(Arr::only($data, ['name', 'is_active']));
        foreach($group->raffles as $raffle) $raffle->update(['is_active' => $data['is_active']]);
        DB::commit();
        return $group;
    }  

    /**
     * Deletes a raffle group. 
     *
     * @param  \App\Models\Raffle\RaffleGroup $raffle
     * @return bool
     */
    public function deleteRaffleGroup($group) 
    {
        DB::beginTransaction();
        foreach($group->raffles as $raffle) $raffle->update(['group_id' => null]);
        $group->delete();
        DB::commit();
        return true;
    }   
}
