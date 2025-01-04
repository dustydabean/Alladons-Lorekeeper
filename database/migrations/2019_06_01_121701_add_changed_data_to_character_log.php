<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChangedDataToCharacterLog extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('character_log', function (Blueprint $table) {
            // This will store the specifics of the changes made
            $table->text('change_log')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('character_log', function (Blueprint $table) {
            //
            $table->dropColumn('change_log');
        });
    }
}
