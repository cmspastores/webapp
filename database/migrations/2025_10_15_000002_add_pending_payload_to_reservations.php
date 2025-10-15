<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPendingPayloadToReservations extends Migration
{
    public function up()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->json('pending_payload')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('pending_payload');
        });
    }
}