<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCharacterLineageTable extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        // Create tables for storing character lineages.
        Schema::dropIfExists('character_lineages');
        Schema::create('character_lineages', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('character_id')->unsigned()->unique();
            $table->integer('father_id')->nullable()->default(null);
            $table->string('father_name')->nullable()->default(null);
            $table->integer('mother_id')->nullable()->default(null);
            $table->string('mother_name')->nullable()->default(null);
            $table->integer('depth')->default(0); // how many generations back this lineage begins

            $table->foreign('character_id')->references('id')->on('characters');
        });

        // ------------------------------------------
        // id | type     | type_id | complete_removal
        // ---|----------|---------|-----------------
        //  x | category | catID   | true (blacklist)
        //  x | species  | sID     | false (greylist)
        //  x | subtype  | stID    | true (blacklist)
        // ------------------------------------------
        // blacklist > greylist > default
        // ------------------------------------------
        Schema::create('character_lineage_blacklist', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('type');
            $table->integer('type_id')->unsigned();
            $table->boolean('complete_removal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::dropIfExists('character_lineages');
        Schema::dropIfExists('character_lineage_blacklist');
    }
}
