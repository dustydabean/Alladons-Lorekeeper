<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLociAlleles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loci_alleles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loci_id')->constrained();
            $table->boolean('is_dominant');
            $table->string('modifier', 5)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loci_alleles');
    }
}
