<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('user_pets_log', function (Blueprint $table) {
            $table->text('log')->change();
            $table->text('data')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('user_pets_log', function (Blueprint $table) {
            $table->string('log')->change();
            $table->string('data', 1024)->nullable()->change();
        });
    }
};
