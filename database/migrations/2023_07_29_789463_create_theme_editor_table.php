<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThemeEditorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('theme_editor', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->string('name');
            $table->string('title_color')->default("#ffffff");
            $table->string('nav_color')->default("#343a40");
            $table->string('nav_text_color')->default("#ffffff");

            $table->string('header_image_display')->default("inline");
            $table->string('header_image_url')->default("/images/header.png");

            $table->string('background_color')->default("#ddd");
            $table->string('background_image_url')->default('');
            $table->string('background_size')->default('cover');

            $table->string('main_color')->default('#fff');
            $table->string('main_text_color')->default('#000');

            $table->string('card_color')->default('#fff');
            $table->string('card_header_color')->default('#f1f1f1');
            $table->string('card_text_color')->default('#000');

            $table->string('link_color')->default('#000');
            $table->string('primary_button_color')->default('#007bff');
            $table->string('secondary_button_color')->default('#6c757d');

            $table->boolean('is_released')->default(0);


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('theme_editor');
    }
}
