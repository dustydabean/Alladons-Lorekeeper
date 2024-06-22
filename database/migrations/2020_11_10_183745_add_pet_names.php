<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPetNames extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        //
        Schema::table('user_pets', function (Blueprint $table) {
            //
            $table->string('pet_name')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        //
        Schema::table('user_pets', function (Blueprint $table) {
            //
            $table->dropColumn('pet_name');
        });
    }
}
