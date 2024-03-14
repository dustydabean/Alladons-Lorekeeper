<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddThemeIdToThemeEditor extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('theme_editor', function (Blueprint $table) {
            $table->integer('theme_id')->nullable()->unsigned()->default(null);
        });

        Schema::table('themes', function (Blueprint $table) {
            $table->boolean('prioritize_css')->default(false);
            $table->integer('link_id')->unsigned()->nullable()->default(null);
            $table->string('link_type')->nullable()->default(null);
            // By default - item granted themes will override
            $table->boolean('is_user_selectable')->default(false);
        });

        Schema::create('user_themes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('theme_id')->unsigned();
            $table->integer('user_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('theme_editor', function (Blueprint $table) {
            $table->dropColumn('theme_id');
        });

        Schema::table('themes', function (Blueprint $table) {
            $table->dropColumn('prioritize_css');
            $table->dropColumn('link_id');
            $table->dropColumn('link_type');
            $table->dropColumn('is_user_selectable');
        });

        Schema::dropIfExists('user_themes');
    }
}
