<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLociAllelesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loci_alleles', function (Blueprint $table) {
            $table->unsignedInteger('sort')->default(0)->after('is_dominant');
            $table->string('name', 5)->after('sort');
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
            $table->dropColumn('name');
            $table->dropColumn('sort');
        });
    }
}
