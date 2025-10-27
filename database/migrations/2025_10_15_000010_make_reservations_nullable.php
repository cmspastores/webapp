<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Adjust types if your schema differs (check SHOW CREATE TABLE `reservations`)
        DB::statement("ALTER TABLE `reservations` MODIFY `agreement_id` BIGINT UNSIGNED NULL");
        DB::statement("ALTER TABLE `reservations` MODIFY `room_id` BIGINT UNSIGNED NULL");
        DB::statement("ALTER TABLE `reservations` MODIFY `first_name` VARCHAR(255) NULL");
        DB::statement("ALTER TABLE `reservations` MODIFY `last_name` VARCHAR(255) NULL");
        DB::statement("ALTER TABLE `reservations` MODIFY `reservation_type` VARCHAR(255) NULL");
        DB::statement("ALTER TABLE `reservations` MODIFY `status` VARCHAR(255) NOT NULL DEFAULT 'unverified'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE `reservations` MODIFY `agreement_id` BIGINT UNSIGNED NOT NULL");
        DB::statement("ALTER TABLE `reservations` MODIFY `room_id` BIGINT UNSIGNED NOT NULL");
        DB::statement("ALTER TABLE `reservations` MODIFY `first_name` VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE `reservations` MODIFY `last_name` VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE `reservations` MODIFY `reservation_type` VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE `reservations` MODIFY `status` VARCHAR(255) NOT NULL");
    }
};