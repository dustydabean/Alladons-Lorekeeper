<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLociIdToCharacterGenomeGenes extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('character_genome_genes', function (Blueprint $table) {
            $table->foreignId('loci_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('character_genome_genes', function (Blueprint $table) {
            $table->dropForeign('character_genome_genes_loci_id_foreign');
            $table->dropColumn('loci_id');
        });
    }
}
