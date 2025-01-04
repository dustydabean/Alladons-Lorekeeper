<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBans extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        //
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_banned')->default(0);
        });
        Schema::table('user_settings', function (Blueprint $table) {
            $table->timestamp('banned_at')->nullable()->default(null);
            $table->text('ban_reason')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        //
        Schema::table('user_settings', function (Blueprint $table) {
            $table->dropColumn('ban_reason');
            $table->dropColumn('banned_at');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_banned');
        });
    }
}
