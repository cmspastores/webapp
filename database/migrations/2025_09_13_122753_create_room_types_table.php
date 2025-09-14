<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. Dorm/Single, Dorm/Double, etc.
            $table->timestamps();
        });

        // Update rooms to reference room_types
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('room_type'); // remove old string column
            $table->foreignId('room_type_id')->nullable()->constrained('room_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};
