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
        Schema::create('renters', function (Blueprint $table) {
            $table->bigIncrements('renter_id'); // Custom PK
            $table->string('first_name');
            $table->string('last_name');
            $table->date('dob')->nullable(); // Date of Birth
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('renters');
    }
};
