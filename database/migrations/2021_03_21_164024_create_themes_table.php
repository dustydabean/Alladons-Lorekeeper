<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThemesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('themes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name'); // Name for users to see
            $table->string('hash'); // Hash to avoid cache issues

            $table->boolean('is_default')->default(0);  // There can only be one default theme at a time.
            $table->boolean('is_active')->default(1); // Active means visible for users.

            $table->boolean('has_css')->default(0); // css file uploaded to the theme folder
            $table->boolean('has_header')->default(0); // header image uploaded to the theme folder
            $table->string('extension',5)->nullable()->default(null); // Header image extension

            $table->text('creators'); // JSON Credits to creators

            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->integer('theme_id')->unsigned()->index()->nullable()->default(null); // Name for users to see
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('theme_id');
        });
        Schema::dropIfExists('themes');
    }
}
