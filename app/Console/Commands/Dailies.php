<?php

namespace App\Console\Commands;

use DB;
use Config;
use Illuminate\Console\Command;
use App\Models\User\User;

class Dailies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dailies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset database user->foraging to 0 every 24 hours';

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
        DB::table('user_foraginge')->where('foraged', '=', 1)->update(['foraged' => 0 ]);
    }
}