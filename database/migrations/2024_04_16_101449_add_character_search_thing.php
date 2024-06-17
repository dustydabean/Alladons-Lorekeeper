<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCharacterSearchThing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //i genuinely dont know what to call this so i'll call it theme for now 
        //i'll add lang anyway so i don't think it will matter (that much hopefully)
        Schema::table('character_images', function (Blueprint $table) {
            $table->string('theme', 256)->nullable()->default(null);
        });

        Schema::table('design_updates', function (Blueprint $table) {
            $table->string('theme', 256)->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('character_images', function(Blueprint $table) {
            $table->dropColumn('theme');
        });
        Schema::table('design_updates', function(Blueprint $table) {
            $table->dropColumn('theme');
        });
    }
}
