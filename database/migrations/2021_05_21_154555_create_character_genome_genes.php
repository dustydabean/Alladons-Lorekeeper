<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCharacterGenomeGenes extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('character_genome_genes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('character_genome_id')->constrained();
            $table->foreignId('loci_allele_id')->constrained();
        });

        Schema::create('character_genome_gradients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('character_genome_id')->constrained();
            $table->foreignId('loci_id')->constrained();
            $table->binary('value', 20);
        });

        Schema::create('character_genome_numerics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('character_genome_id')->constrained();
            $table->foreignId('loci_id')->constrained();
            $table->unsignedTinyInteger('value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::dropIfExists('character_genome_genes');
        Schema::dropIfExists('character_genome_gradients');
        Schema::dropIfExists('character_genome_numerics');
    }
}
