<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdminOnlyToSitePagesTable extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('site_pages', function (Blueprint $table) {
            $table->boolean('admin_only')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('site_pages', function (Blueprint $table) {
            $table->dropColumn('admin_only');
        });
    }
}
