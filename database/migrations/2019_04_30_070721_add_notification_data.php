<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotificationData extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        //
        Schema::table('notifications', function (Blueprint $table) {
            $table->string('data', 1024)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        //
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn('data');
        });
    }
}
