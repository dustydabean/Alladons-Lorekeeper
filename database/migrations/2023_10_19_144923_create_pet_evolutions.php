<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePetEvolutions extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('pet_evolutions', function (Blueprint $table) {
            $table->id();
            $table->integer('pet_id');
            $table->string('evolution_name');
            $table->integer('evolution_stage');
            $table->string('variants')->nullable()->default(null);
        });

        Schema::table('user_pets', function (Blueprint $table) {
            $table->integer('evolution_id')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::dropIfExists('pet_evolutions');
        Schema::table('user_pets', function (Blueprint $table) {
            $table->dropColumn('evolution_id');
        });
    }
}
