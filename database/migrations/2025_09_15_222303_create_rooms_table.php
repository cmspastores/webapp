<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number')->unique(); 
            $table->string('room_type'); 
            $table->decimal('room_price', 10, 2); 
            $table->unsignedInteger('number_of_occupants')->nullable();
            $table->string('occupant_name')->nullable(); // for now, just string. Later can be linked to Customers
            $table->date('start_date')->nullable();

            $table->string('image')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};

