<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCharacterProfileCustomValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_profile_custom_values', function (Blueprint $table) {
            $table->bigInteger('character_id')->references('id')->on('characters');
            $table->string('group', 50)->nullable()->default(null);
            $table->string('name', 50)->nullable()->default(null);
            $table->string('data', 256);
            $table->string('data_parsed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('character_profile_custom_values');
    }
}
