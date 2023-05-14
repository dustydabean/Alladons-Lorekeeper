<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTransformations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('transformations', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->boolean('has_image')->default(0);
            $table->integer('sort')->unsigned()->default(0);
            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);

        });
        Schema::table('character_images', function(Blueprint $table) {
            $table->integer('transformation_id')->unsigned()->nullable()->default(null);
            $table->boolean('has_transformation')->default(0);
        });
        Schema::table('design_updates', function(Blueprint $table) {
            $table->integer('transformation_id')->unsigned()->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('character_images', function(Blueprint $table) {
            $table->dropColumn('transformation_id');
        });
        Schema::table('design_updates', function(Blueprint $table) {
            $table->dropColumn('transformation_id');
        });
        Schema::dropIfExists('transformations');
    }
}
