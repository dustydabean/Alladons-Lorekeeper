<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        //Create the Main Index Table
        Schema::create('site_index', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id');
            $table->string('title', 1024)->nullable();
            $table->string('type', 300)->nullable();
            $table->string('identifier', 300)->nullable();
            $table->string('description', 1024)->nullable();
        });

        //Create the Temp Index Table
        Schema::create('site_temp_index', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id');
            $table->string('title', 1024)->nullable();
            $table->string('type', 300)->nullable();
            $table->string('identifier', 300)->nullable();
            $table->string('description', 1024)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        //Remove if down
        Schema::dropIfExists('site_index');
        Schema::dropIfExists('site_temp_index');
    }
};
