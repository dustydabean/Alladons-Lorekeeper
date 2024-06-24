<?php

namespace App\Console\Commands;

use App\Models\Character\CharacterRelation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class UpdateCharacterRelations extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-character-relations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates existing character relations in preperation for the new character relations table.';

    /**
     * Execute the console command.
     */
    public function handle() {
        //
        if (Schema::hasColumn('character_relations', 'character_1_id')) {
            $this->info('Character relations have already been updated.');

            return;
        }

        $this->info('Updating character relations...');

        $initialRelations = CharacterRelation::all();

        $this->info("found {$initialRelations->count()} relations to update.");

        $bar = $this->output->createProgressBar($initialRelations->count());
        $bar->start();
        $seen = [];
        foreach ($initialRelations as $relation) {
            $alternateRelation = CharacterRelation::where('chara_1', $relation->chara_2)
                ->where('chara_2', $relation->chara_1)
                ->first();

            if (in_array($relation->id, $seen) || in_array($alternateRelation?->id, $seen)) {
                $bar->advance();
                continue;
            }

            // whichever relation has the lowest chara_1 is the one we want to keep
            if ($alternateRelation) {
                $relation->update([
                    'chara_1' => $alternateRelation->chara_1 < $relation->chara_1 ? $alternateRelation->chara_1 : $relation->chara_1,
                    'chara_2' => $alternateRelation->chara_1 < $relation->chara_1 ? $alternateRelation->chara_2 : $relation->chara_2,
                    'info'    => json_encode([
                        0 => $alternateRelation->chara_1 < $relation->chara_1 ? $alternateRelation->info : $relation->info,
                        1 => $alternateRelation->chara_1 < $relation->chara_1 ? $relation->info : $alternateRelation->info,
                    ]),
                    'type'   => $relation->type == $alternateRelation->type ? $relation->type : '???',
                    'status' => // if both are approved, keep approved, otherwise keep pending
                        $relation->status == 'Approved' && $alternateRelation->status == 'Approved' ? 'Approved' : 'Pending',
                ]);

                $seen[] = $alternateRelation->id;
                $alternateRelation->delete();
            }

            $seen[] = $relation->id;
            $bar->advance();
        }

        $bar->finish();

        $this->info("\nCharacter relations update and deduplication complete.");
    }
}
