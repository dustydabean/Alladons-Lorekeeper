<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToDesignUpdates extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('design_updates', function (Blueprint $table) {
            //
            $table->enum('update_type', ['MYO', 'Character'])->nullable()->default('Character');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('design_updates', function (Blueprint $table) {
            //
            $table->dropColumn('update_type');
        });
    }
}
