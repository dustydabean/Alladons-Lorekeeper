<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNullableToThemeEditor extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('theme_editor', function (Blueprint $table) {
            $table->string('title_color')->nullable()->default(null)->change();
            $table->string('nav_color')->nullable()->default(null)->change();
            $table->string('nav_text_color')->nullable()->default(null)->change();

            $table->string('header_image_display')->nullable()->default(null)->change();
            $table->string('header_image_url')->nullable()->default(null)->change();

            $table->string('background_color')->nullable()->default(null)->change();
            $table->string('background_image_url')->nullable()->default(null)->change();
            $table->string('background_size')->nullable()->default(null)->change();

            $table->string('main_color')->nullable()->default(null)->change();
            $table->string('main_text_color')->nullable()->default(null)->change();

            $table->string('card_color')->nullable()->default(null)->change();
            $table->string('card_header_color')->nullable()->default(null)->change();
            $table->string('card_text_color')->nullable()->default(null)->change();

            $table->string('link_color')->nullable()->default(null)->change();
            $table->string('primary_button_color')->nullable()->default(null)->change();
            $table->string('secondary_button_color')->nullable()->default(null)->change();
            $table->string('card_header_text_color')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('theme_editor', function (Blueprint $table) {
            $table->string('title_color')->default("#ffffff")->change();
            $table->string('nav_color')->default("#343a40")->change();
            $table->string('nav_text_color')->default("#ffffff")->change();

            $table->string('header_image_display')->default("inline")->change();
            $table->string('header_image_url')->default("/images/header.png")->change();

            $table->string('background_color')->default("#ddd")->change();
            $table->string('background_image_url')->default('')->change();
            $table->string('background_size')->default('cover')->change();

            $table->string('main_color')->default('#fff')->change();
            $table->string('main_text_color')->default('#000')->change();

            $table->string('card_color')->default('#fff')->change();
            $table->string('card_header_color')->default('#f1f1f1')->change();
            $table->string('card_text_color')->default('#000')->change();

            $table->string('link_color')->default('#000')->change();
            $table->string('primary_button_color')->default('#007bff')->change();
            $table->string('secondary_button_color')->default('#6c757d')->change();
            $table->string('card_header_text_color')->default('#000')->change();
        });
    }
}
