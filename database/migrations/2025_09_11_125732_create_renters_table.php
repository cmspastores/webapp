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
            $table->bigIncrements('renter_id'); // Internal PK (auto increment)
            $table->string('unique_id')->unique(); 

            // Names
            $table->string('first_name');
            $table->string('last_name');
            $table->string('full_name'); // stored for faster queries/search

            // Personal details
            $table->date('dob')->nullable(); 

            // Contact details
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('emergency_contact')->nullable();

            // Guardian details
            $table->string('guardian_name')->nullable();    // Added guardian_name
            $table->string('guardian_phone')->nullable();   // Added guardian_phone
            $table->string('guardian_email')->nullable();   // Added guardian_email

            // Room number (nullable for now to avoid errors)
            $table->string('room_number')->nullable();
            
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
