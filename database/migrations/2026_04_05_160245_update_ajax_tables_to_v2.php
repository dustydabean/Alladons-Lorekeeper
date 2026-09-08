<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('site_index', function (Blueprint $table) {
            $table->string('key')->after('type')->nullable();
            $table->string('url')->after('description')->nullable();
            $table->string('image_url')->after('url')->nullable();
        });
        Schema::table('site_temp_index', function (Blueprint $table) {
            $table->string('key')->after('type')->nullable();
            $table->string('url')->after('description')->nullable();
            $table->string('image_url')->after('url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('site_index', function (Blueprint $table) {
            $table->dropColumn('key');
            $table->dropColumn('url');
            $table->dropColumn('image_url');
        });
        Schema::table('site_temp_index', function (Blueprint $table) {
            $table->dropColumn('key');
            $table->dropColumn('url');
            $table->dropColumn('image_url');
        });
    }
};
