<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDevLogNotifToUserSettingsTable extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('user_settings', function (Blueprint $table) {
            $table->tinyInteger('dev_log_notif')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('user_settings', function (Blueprint $table) {
            $table->dropColumn('dev_log_notif');
        });
    }
}
