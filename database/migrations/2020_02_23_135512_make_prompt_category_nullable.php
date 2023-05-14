<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakePromptCategoryNullable extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        //
        Schema::table('prompts', function (Blueprint $table) {
            $table->integer('prompt_category_id')->unsigned()->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        //
        Schema::table('prompts', function (Blueprint $table) {
            $table->integer('prompt_category_id')->unsigned()->default(0)->change();
        });
    }
}
