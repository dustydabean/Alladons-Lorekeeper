<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Shop\ShopStock;
use Carbon\Carbon;

class update_timed_stock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-timed-stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hides timed stock when expired, or sets it active if ready.';

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
     * @return int
     */
    public function handle()
    { 
        $hidestock = ShopStock::where('is_timed_stock', 1)->where('is_visible', 1)->where('start_at', '<=', Carbon::now())->where('end_at', '<=', Carbon::now())->get();
        $showstock = ShopStock::where('is_timed_stock', 1)->where('is_visible', 0)->where('start_at', '<=', Carbon::now())->where('end_at', '>=', Carbon::now())->get();
        //set stock that should be active to active
        foreach($showstock as $showstock) { 
            $showstock->is_visible = 1;
            $showstock->save();
        } 
        //hide stock that should be hidden now
        foreach($hidestock as $hidestock) { 
                $hidestock->is_visible = 0;
                $hidestock->save();
            } 
    }
}
