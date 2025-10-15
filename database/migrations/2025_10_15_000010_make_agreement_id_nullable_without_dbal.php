<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MakeAgreementIdNullableWithoutDbal extends Migration
{
    public function up()
    {
        // Adjust the column type below to match your schema if it's INT/SMALLINT/etc.
        DB::statement("ALTER TABLE `reservations` MODIFY `agreement_id` BIGINT UNSIGNED NULL");
    }

    public function down()
    {
        DB::statement("ALTER TABLE `reservations` MODIFY `agreement_id` BIGINT UNSIGNED NOT NULL");
    }
}