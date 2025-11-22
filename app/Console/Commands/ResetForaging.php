<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Settings;
use App\Models\User\UserForaging;

class ResetForaging extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset-foraging';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resets foraging stamina for users to setting amount.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        // update all user models with foraging stamina
        UserForaging::all()->each(function($userForaging) {
            $userForaging->stamina = Settings::get('foraging_stamina');
            $userForaging->save();
        });
    }
}
