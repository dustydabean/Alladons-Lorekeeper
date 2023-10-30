<?php

use Illuminate\Database\Migrations\Migration;

class SetDesignUpdateTypeNull extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        //Change default to null going forward
        DB::statement("ALTER TABLE design_updates CHANGE COLUMN update_type update_type ENUM('MYO', 'Character') DEFAULT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        //
        DB::statement("ALTER TABLE design_updates CHANGE COLUMN update_type update_type ENUM('MYO', 'Character') DEFAULT 'Character'");
    }
}
