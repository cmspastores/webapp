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
        Schema::create('agreements', function (Blueprint $table) {
            $table->bigIncrements('agreement_id');

            // Foreign keys
            $table->unsignedBigInteger('renter_id');
            $table->foreign('renter_id')
                  ->references('renter_id')
                  ->on('renters')
                  ->onDelete('cascade');

            $table->unsignedBigInteger('room_id');
            $table->foreign('room_id')
                  ->references('id')
                  ->on('rooms')
                  ->onDelete('cascade');

            // Agreement details
            $table->date('agreement_date');       // Date signed
            $table->date('start_date');           // Start of rental
            $table->date('end_date');             // 1 year from start_date

            // For future billing
            $table->decimal('monthly_rent', 10, 2)->nullable();  

            // Renewal tracking
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agreements');
    }
};