<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStaffIdToTrades extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('trades', function (Blueprint $table) {
            // Add a staff ID so we know who processed the trade, if applicable
            $table->integer('staff_id')->unsigned()->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('trades', function (Blueprint $table) {
            $table->dropColumn('staff_id');
        });
    }
}
