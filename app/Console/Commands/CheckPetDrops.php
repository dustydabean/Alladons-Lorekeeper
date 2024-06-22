<?php

namespace App\Console\Commands;

use App\Models\Pet\PetDrop;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckPetDrops extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check-pet-drops';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks if there are any pet drops to update.';

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
        //
        $updateDrops = PetDrop::requiresUpdate()->get();
        foreach ($updateDrops as $drop) {
            if ((!isset($drop->dropData->cap) || $drop->dropData->cap == 0) || $drop->drops_available < $drop->dropData->cap) {
                $drop->update([
                    'drops_available' => $drop->drops_available += 1,
                    'next_day'        => Carbon::now()->add(
                        $drop->dropData->frequency,
                        $drop->dropData->interval,
                    )->startOf($drop->dropData->interval),
                ]);
            }
        }
    }
}
