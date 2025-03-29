<?php

namespace App\Console\Commands;

use App\Models\Feature\Feature;
use App\Models\Feature\FeatureExample;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class updatetraitexamples extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-trait-examples';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Turn example images into their own table with all existing data';

    /**
     * Create a new command instance.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        //this is probably messy and not optimal :')

        if (Schema::hasTable('feature_example_images')) {
            $this->info('Already migrated new tables. Moving and renaming images instead. ');
        } else {
            Schema::create('feature_example_images', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->integer('feature_id');
                $table->string('summary', 256)->nullable()->default(null);
                $table->string('hash', 10)->nullable();
                $table->integer('sort')->unsigned()->default(0);
            });
        }

        if (Schema::hasColumn('features', 'has_example_image')) {
            //convert all the examples
            $features = Feature::where('has_example_image', 1)->get();

            if ($features->count()) {
                foreach ($features as $feature) {
                    $old_image = $feature->imageDirectory.'/'.$feature->exampleImageFileName;

                    //move the image
                    if (File::exists(public_path($old_image))) {
                        //make the new example
                        $example = FeatureExample::create([
                            'summary'    => $feature->example_summary,
                            'hash'       => $feature->example_hash,
                            'feature_id' => $feature->id,
                        ]);

                        // Make the image directory if it doesn't exist
                        if (!file_exists($example->imagePath)) {
                            // Create the directory.
                            if (!mkdir($example->imagePath, 0755, true)) {
                                $this->setError('error', 'Failed to create image directory.');

                                return false;
                            }
                            chmod($example->imagePath, 0755);
                        }

                        $new_image = $example->imageDirectory.'/'.$example->imageFileName;
                        if (!File::move(public_path($old_image), public_path($new_image))) {
                            $this->error('Failed to move example for '.$feature->name.', skipping.');
                        } else {
                            $feature->update([
                                'has_example_image' => 0,
                                'example_summary'   => null,
                                'example_hash'      => null,
                            ]);
                        }
                    } else {
                        $this->error('Image for '.$feature->name.'does not exist.');
                        $feature->update([
                            'has_example_image' => 0,
                            'example_summary'   => null,
                            'example_hash'      => null,
                        ]);
                    }
                }
            } else {
                if (Schema::hasColumn('features', 'has_example_image')) {
                    $this->info('No more example images to export, deleting previous columns...');

                    Schema::table('features', function (Blueprint $table) {
                        $table->dropColumn('has_example_image');
                        $table->dropColumn('example_summary');
                        $table->dropColumn('example_hash');
                    });

                    $this->info('All exports complete!');
                } else {
                    $this->error('Why are you running this command after it\'s done??????? Go home');
                }
            }
        } else {
            $this->error('Why are you running this command after it\'s done??????? Go home');
        }
    }
}
