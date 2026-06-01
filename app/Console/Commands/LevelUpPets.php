<?php

namespace App\Console\Commands;

use App\Facades\Settings;
use App\Models\User\UserPet;
use App\Services\PetManager;
use Illuminate\Console\Command;

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
    protected $description = 'Processes scheduled level-ups (and level-downs) for any pets whose bonding has crossed a threshold.';

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
        $petManager = new PetManager;
        $maxLevel = Settings::get('max_pet_level');

        $userPets = UserPet::whereHas('level', function ($query) use ($maxLevel) {
            $query->whereNotNull('next_level_at');
            if ($maxLevel) {
                $query->where('bonding_level', '<', $maxLevel);
            }
        })->with('level', 'pet')->get();

        foreach ($userPets as $userPet) {
            $petManager->processLevelChange($userPet);
        }
    }
}
