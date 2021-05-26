<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCharacterBreedingLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_breeding_logs', function (Blueprint $table) {
            $table->id();
            $table->string("name")->nullable();
            $table->json("roller_settings");
            $table->timestamp("rolled_at");
        });

        Schema::create('character_breeding_log_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('log_id')->constrained('character_breeding_logs');
            $table->unsignedInteger("character_id");
            $table->foreign('character_id')->references('id')->on('characters');
            $table->boolean("is_parent");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('character_breeding_log_relations');
        Schema::dropIfExists('character_breeding_logs');
    }
}
