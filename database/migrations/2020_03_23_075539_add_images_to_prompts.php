<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImagesToPrompts extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('prompts', function (Blueprint $table) {
            //
            $table->boolean('has_image')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('prompts', function (Blueprint $table) {
            //
            $table->dropColumn('has_image');
        });
    }
}
