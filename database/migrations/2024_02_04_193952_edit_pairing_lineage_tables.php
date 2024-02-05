<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditPairingLineageTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::table('character_lineages', function (Blueprint $table) {
            // first we're gonna rename father_id and mother_id to parent ids
            // since pairings can allow same-sex parents
            $table->renameColumn('father_id', 'parent_1_id');
            $table->renameColumn('father_name', 'parent_1_name');

            $table->renameColumn('mother_id', 'parent_2_id');
            $table->renameColumn('mother_name', 'parent_2_name');
        });

        Schema::table('character_images', function (Blueprint $table) {
            $table->string('colours')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('character_lineages', function (Blueprint $table) {
            $table->renameColumn('parent_1_id', 'father_id');
            $table->renameColumn('parent_1_name', 'father_name');
            $table->renameColumn('parent_2_id', 'mother_id');
            $table->renameColumn('parent_2_name', 'mother_name');
        });

        Schema::table('character_images', function (Blueprint $table) {
            $table->dropColumn('colours');
        });
    }
};
