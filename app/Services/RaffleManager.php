<?php namespace App\Services;

use DB;
use Carbon\Carbon;
use App\Services\Service;
use App\Models\Raffle\RaffleGroup;
use App\Models\Raffle\Raffle;
use App\Models\Raffle\RaffleTicket;
use App\Models\User\User;

class RaffleManager extends Service 
{
    /*
    |--------------------------------------------------------------------------
    | Raffle Manager
    |--------------------------------------------------------------------------
    |
    | Handles creation and modification of raffle ticket data.
    |
    */

    /**
     * Adds tickets to a raffle. 
     * One ticket is added per name in $names, which is a
     * string containing comma-separated names.
     *
     * @param  \App\Models\Raffle\Raffle $raffle
     * @param  string                    $names
     * @return int
     */
    public function addTickets($raffle, $names)
    {
        $names = explode(',', $names);
        $count = 0;
        foreach($names as $name)
        {
            $name = trim($name);
            if(strlen($name) == 0) continue;
            if ($user = User::where('name', $name)->first())
                $count += $this->addTicket($user, $raffle);
            else
                $count += $this->addTicket($name, $raffle);
        }
        return $count;
    }

    /**
     * Adds one or more tickets to a single user for a raffle.
     *
     * @param  \App\Models\User\User     $user
     * @param  \App\Models\Raffle\Raffle $raffle
     * @param  int                       $count
     * @return int
     */
    public function addTicket($user, $raffle, $count = 1)
    {
        if (!$user) return 0;
        else if (!$raffle) return 0;
        else if ($count == 0) return 0;
        else if ($raffle->rolled_at != null) return 0;
        else {
            DB::beginTransaction();
            $data = ["raffle_id" => $raffle->id, 'created_at' => Carbon::now()] + (is_string($user) ? ['alias' => $user] : ['user_id' => $user->id]);
            for ($i = 0; $i < $count; $i++) RaffleTicket::create($data);
            DB::commit();
            return 1;
        }
        return 0;
    }

    /**
     * Removes a single ticket.
     *
     * @param  \App\Models\Raffle\RaffleTicket $ticket
     * @return bool
     */
    public function removeTicket($ticket)
    {
        if (!$ticket) return null;
        else {
            $ticket->delete();
            return true;
        }
        return false;
    }

    /**
     * Rolls a raffle group consecutively.
     * If the $updateGroup flag is true, winners will be removed
     * from other raffles in the group.
     *
     * @param  \App\Models\Raffle\RaffleGroup $raffleGroup
     * @param  bool                           $updateGroup
     * @return bool
     */
    public function rollRaffleGroup($raffleGroup, $updateGroup = true)
    {
        if(!$raffleGroup) return null;
        DB::beginTransaction();
        foreach($raffleGroup->raffles()->orderBy('order')->get() as $raffle)
        {
            if (!$this->rollRaffle($raffle, $updateGroup)) 
            {
                DB::rollback();
                return false;
            }
        }
        $raffleGroup->is_active = 2;
        $raffleGroup->save();
        DB::commit();
        return true;
    }

    /**
     * Rolls a single raffle and marks it as completed.
     * If the $updateGroup flag is true, winners will be removed
     * from other raffles in the group.
     *
     * @param  \App\Models\Raffle\Raffle $raffle
     * @param  bool                      $updateGroup
     * @return bool
     */
    public function rollRaffle($raffle, $updateGroup = false) 
    {
        if(!$raffle) return null;
        DB::beginTransaction();
        // roll winners
        if($winners = $this->rollWinners($raffle))
        {
            // mark raffle as finished
            $raffle->is_active = 2;
            $raffle->rolled_at = Carbon::now();
            $raffle->save();

            // updates the raffle group if necessary
            if($updateGroup && !$this->afterRoll($winners, $raffle->group, $raffle))
            {
                DB::rollback();
                return false;
            }
            DB::commit();
            return true;
        }
        DB::rollback();
        return false;
    }

    /**
     * Rolls the winners of a raffle.
     *
     * @param  \App\Models\Raffle\Raffle $raffle
     * @return array
     */
    private function rollWinners($raffle)
    {
        $ticketPool = $raffle->tickets;
        $ticketCount = $ticketPool->count();
        $winners = ['ids' => [], 'aliases' => []];
        for ($i = 0; $i < $raffle->winner_count; $i++)
        {
            if($ticketCount == 0) break;

            $num = mt_rand(0, $ticketCount - 1);
            $winner = $ticketPool[$num];

            // save ticket position as ($i + 1)
            $winner->update(['position' => $i + 1]);

            // save the winning ticket's user id
            if(isset($winner->user_id)) $winners['ids'][] = $winner->user_id;
            else $winners['aliases'][] = $winner->alias;

            // remove ticket from the ticket pool after pulled
            $ticketPool->forget($num);
            $ticketPool = $ticketPool->values();

            $ticketCount--;

            // remove tickets for the same user...I'm unsure how this is going to hold up with 3000 tickets,
            foreach($ticketPool as $key=>$ticket)
            {
                if(($ticket->user_id != null && $ticket->user_id == $winner->user_id) || ($ticket->user_id == null && $ticket->alias == $winner->alias)) 
                {
                    $ticketPool->forget($key);
                }

            }
            $ticketPool = $ticketPool->values();
            $ticketCount = $ticketPool->count();
        }
        return $winners;
    }

    /**
     * Rolls the winners of a raffle.
     *
     * @param  array                          $winners
     * @param  \App\Models\Raffle\RaffleGroup $raffleGroup
     * @param  \App\Models\Raffle\Raffle      $raffle
     * @return bool
     */
    private function afterRoll($winners, $raffleGroup, $raffle)
    {
        // remove any tickets from winners in raffles in the group that aren't completed
        $raffles = $raffleGroup->raffles()->where('is_active', '!=', 2)->where('id', '!=', $raffle->id)->get();
        foreach($raffles as $r)
        {
            $r->tickets()->where(function($query) use ($winners) { 
                $query->whereIn('user_id', $winners['ids'])->orWhereIn('alias', $winners['aliases']); 
            })->delete();
        }
        return true;
    }


}
