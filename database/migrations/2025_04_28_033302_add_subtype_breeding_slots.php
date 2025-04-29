<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subtypes', function (Blueprint $table) {
            $table->integer('breeding_slot_amount')->unsigned()->nullable()->default(null);
        });

        Schema::create('character_breeding_slots', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('character_id')->unsigned();
            $table->integer('offspring_id')->unsigned()->nullable()->default(null);

            $table->integer('user_id')->unsigned()->nullable()->default(null);
            $table->string('user_url')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subtypes', function (Blueprint $table) {
            $table->dropColumn('breeding_slot_amount');
        });
        Schema::dropIfExists('character_breeding_slots');
    }
};
