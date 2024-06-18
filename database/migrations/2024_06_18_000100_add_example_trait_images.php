<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExampleTraitImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('features', function(Blueprint $table) {
            $table->boolean('has_example_image')->default(0);
            $table->string('example_summary', 256)->nullable()->default(null);
            $table->string('example_hash', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('features', function (Blueprint $table) {
            $table->dropColumn('has_example_image');
            $table->dropColumn('example_summary');
            $table->dropColumn('example_hash');
        });
    }
}
