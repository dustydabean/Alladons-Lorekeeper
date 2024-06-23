<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCharacterLinks extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        //
        Schema::create('character_relations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('chara_1');
            $table->integer('chara_2');
            $table->string('info')->nullable();
            $table->string('type')->default('???');
            $table->enum('status', ['Pending', 'Approved'])->default('Pending');
        });

        Schema::table('characters', function (Blueprint $table) {
            $table->boolean('is_links_open')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        //
        Schema::dropIfExists('character_relations');

        Schema::table('characters', function (Blueprint $table) {
            $table->dropColumn('is_links_open');
        });
    }
}
