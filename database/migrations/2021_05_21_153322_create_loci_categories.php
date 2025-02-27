<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLociCategories extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('locis', function (Blueprint $table) {
            $table->id();
            $table->string('name', 25)->unique();
            $table->enum('type', ['gene', 'gradient', 'numeric'])->default('gene');
            $table->unsignedTinyInteger('length')->default(2);
            $table->unsignedTinyInteger('chromosome')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::dropIfExists('locis');
    }
}
