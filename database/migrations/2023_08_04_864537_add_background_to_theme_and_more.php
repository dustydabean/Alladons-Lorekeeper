<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBackgroundToThemeAndMore extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('theme_editor', function (Blueprint $table) {
            $table->string('card_header_text_color')->default('#000');
        });

        Schema::table('themes', function (Blueprint $table) {
            $table->boolean('has_background')->default(0);
            $table->string('extension_background',5)->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('theme_editor', function (Blueprint $table) {
            $table->dropColumn('card_header_text_color');
        });

        Schema::table('themes', function (Blueprint $table) {
            $table->dropColumn('has_background');
            $table->dropColumn('extension_background');
        });
    }
}
