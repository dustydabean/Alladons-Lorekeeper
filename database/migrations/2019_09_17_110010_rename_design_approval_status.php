<?php

use Illuminate\Database\Migrations\Migration;

class RenameDesignApprovalStatus extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        //
        DB::statement("ALTER TABLE design_updates MODIFY COLUMN status ENUM('Draft', 'Pending', 'Approved', 'Rejected')");
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        //
        DB::statement("ALTER TABLE design_updates MODIFY COLUMN status ENUM('Draft', 'Pending', 'Accepted', 'Rejected')");
    }
}
