<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDecoratorThemeToUsers extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('decorator_theme_id')->unsigned()->index()->nullable()->default(null);
        });

        Schema::table('themes', function (Blueprint $table) {
            $table->string('theme_type')->default('base');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('decorator_theme_id');
        });

        Schema::table('themes', function (Blueprint $table) {
            $table->dropColumn('theme_type');
        });
    }
}
