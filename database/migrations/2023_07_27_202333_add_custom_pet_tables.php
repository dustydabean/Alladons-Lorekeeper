<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomPetTables extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('user_pets', function (Blueprint $table) {
            $table->boolean('has_image')->default(0);
            $table->text('description')->nullable()->default(null);
            $table->string('artist_url')->nullable();
            $table->integer('artist_id')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('user_pets', function (Blueprint $table) {
            $table->dropColumn('has_image');
            $table->dropColumn('description');
            $table->dropColumn('artist_url');
            $table->dropColumn('artist_id');
        });
    }
}
