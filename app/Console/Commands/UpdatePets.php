<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class UpdatePets extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-pets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Legacy: converts old format drop data. Pet variants are now first-class pets with parent_id (see update-pet-variants).';

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
        if (!Schema::hasColumn('pet_drop_data', 'name')) {
            Schema::table('pet_drop_data', function ($table) {
                $table->string('name')->default('drop');
                $table->integer('frequency');
                $table->string('interval')->default('Hour');
                $table->integer('cap')->default(null)->nullable();
                $table->boolean('override')->default(false);
            });
        }
        if (!Schema::hasColumn('pet_categories', 'limit')) {
            Schema::table('pet_categories', function ($table) {
                $table->integer('limit')->default(null)->nullable();
            });
        }
        if (!Schema::hasColumn('pets', 'limit')) {
            Schema::table('pets', function ($table) {
                $table->integer('limit')->default(null)->nullable();
            });
        }
        $this->info('Drop data schema verified.');

        // convert old data
        $drop_data = \App\Models\Pet\PetDropData::all();
        foreach ($drop_data as $drop) {
            // check if 'assets' offset exists on $drop->data, if it does continue
            if (isset($drop->data['assets'])) {
                $this->line('Skipping drop data for pet: '.$drop->pet->name.'...');
            } else {
                $this->line('Converting drop data for pet: '.$drop->pet->name.'...');
                $drop->name = $drop->data['drop_name'];
                $drop->frequency = $drop->data['frequency']['frequency'];
                $drop->interval = $drop->data['frequency']['interval'];
                $drop->cap = $drop->data['cap'];
                $this->convertItems($drop, $drop->data['items']);
                $this->info('Converted drop data for pet: '.$drop->pet->name.'.');
            }
        }
    }

    private function convertItems($drop, $data) {
        foreach ($data as $key => $group) {
            $this->line('Converting group: '.$key.'...');
            if ($key == 'pet') {
                $assets = [];
                foreach ($group as $name => $item) {
                    $assets[strtolower($name)]['items'][$item['item_id']] = [
                        'min_quantity' => $item['min'],
                        'max_quantity' => $item['max'],
                    ];
                }
                $drop->data = ['assets' => $assets];
            }
            // variant drop groups are no longer used; variants are first-class pets with their own drop data.
        }
        $drop->save();
    }
}
