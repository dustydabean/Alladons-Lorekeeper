<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('character_pedigrees', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);
        });

        Schema::create('character_generations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);
        });

        Schema::table('characters', function (Blueprint $table) {
            $table->string('nickname')->nullable()->default(null)->after('name');
            $table->integer('pedigree_id')->unsigned()->nullable()->default(null);
            $table->string('pedigree_descriptor')->nullable()->default(null);
            $table->integer('generation_id')->unsigned()->nullable()->default(null);
            $table->timestamp('birthdate')->nullable()->default(null);

            $table->foreign('pedigree_id')->references('id')->on('character_pedigrees');
            $table->foreign('generation_id')->references('id')->on('character_generations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('characters', function (Blueprint $table) {
            $table->dropForeign('characters_pedigree_id_foreign');
            $table->dropForeign('characters_generation_id_foreign');

            $table->dropColumn('nickname');
            $table->dropColumn('pedigree_id');
            $table->dropColumn('generation_id');
            $table->dropColumn('birthdate');
        });

        Schema::dropIfExists('character_pedigrees');
        Schema::dropIfExists('character_generations');
    }
};
