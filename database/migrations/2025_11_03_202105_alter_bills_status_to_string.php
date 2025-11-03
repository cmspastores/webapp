<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // change status from enum to varchar(20)
        DB::statement("ALTER TABLE `bills` MODIFY `status` VARCHAR(20) NOT NULL DEFAULT 'unpaid'");
    }

    public function down(): void
    {
        // revert (optional)
        DB::statement("ALTER TABLE `bills` MODIFY `status` ENUM('unpaid','paid') NOT NULL DEFAULT 'unpaid'");
    }
};