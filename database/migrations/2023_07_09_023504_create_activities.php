<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivities extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('activities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('module');
            $table->text('data')->nullable()->default(null);
            $table->boolean('has_image')->default(0);
            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);
            $table->integer('sort')->unsigned()->default(0);
            $table->boolean('is_active')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::dropIfExists('activities');
    }
}
