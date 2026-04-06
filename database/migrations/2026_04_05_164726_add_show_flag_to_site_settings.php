<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->longText('value')->change();
            $table->boolean('show_in_settings')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn('show_in_settings');
        });
    }
};
