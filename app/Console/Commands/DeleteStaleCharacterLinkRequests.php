<?php

namespace App\Console\Commands;

use App\Models\Character\CharacterRelation;
use Illuminate\Console\Command;

class DeleteStaleCharacterLinkRequests extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete-stale-character-link-requests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes character link requests that have been pending for too long.';

    /**
     * Execute the console command.
     */
    public function handle() {
        //
        $staleLinks = CharacterRelation::where('status', 'Pending')
            ->where('created_at', '<', now()->subDays(1))
            ->get();

        foreach ($staleLinks as $link) {
            $link->delete();
        }
    }
}
