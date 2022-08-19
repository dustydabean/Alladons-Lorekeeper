<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVisibilityToLociAlleles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loci_alleles', function (Blueprint $table) {
            $table->string('summary', 255)->nullable()->default(null);
            $table->boolean('is_visible')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loci_alleles', function (Blueprint $table) {
            $table->dropColumn('summary');
            $table->dropColumn('is_visible');
        });
    }
}
