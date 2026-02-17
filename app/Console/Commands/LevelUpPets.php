<?php

namespace App\Console\Commands;

use App\Facades\Settings;
use App\Models\User\UserPetLevel;
use App\Models\Pet\PetLog;
use Illuminate\Console\Command;
use Carbon\Carbon;

class LevelUpPets extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'level-up-pets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Levels up any eligible pets.';

    /**
     * Create a new command instance.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $today = Carbon::now();
        $userPets = UserPetLevel::has('pet')->where('bonding_level', '<', Settings::get('max_pet_level'))->get()->filter(function ($pet) use ($today) {
            if ($pet->next_level_at && ($pet->levelsAt < $today)) {
                return true;
            }
            return false;
        });

        if ($userPets->count()) {
            $logType = 'Level Up';

            foreach ($userPets as $uPet) {
                $uPet->bonding_level++;
                $uPet->bonding = 0;
                $uPet->next_level_at = Carbon::now()->addYear()->startOfDay();
                $uPet->save();

                $petData = $uPet->pet;
                $logData = 'Pet '.$petData->fullName.' levelled up! It is now level '.$uPet->bonding_level;
                $log = PetLog::create([
                    'sender_id'    => $petData->user_id ?? null,
                    'recipient_id' => $petData->user_id ?? null,
                    'stack_id'     => $uPet->id,
                    'log'          => $logType.($logData ? ' ('.$logData.')' : ''),
                    'log_type'     => $logType,
                    'data'         => $logData,
                    'pet_id'       => $petData->pet_id ?? null,
                    'quantity'     => 1,
                    'created_at'   => Carbon::now(),
                    'updated_at'   => Carbon::now(),
                ]);
            }
        }

    }
}
