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
        Schema::create('bills', function (Blueprint $table) {
            $table->id(); // bill id

            // link to agreement (which includes renter and room)
            $table->unsignedBigInteger('agreement_id');
            $table->foreign('agreement_id')->references('agreement_id')->on('agreements')->onDelete('cascade');

            // Snapshot fields to make statement stable
            $table->unsignedBigInteger('renter_id');
            $table->unsignedBigInteger('room_id');

            // Billing period
            $table->date('period_start');   // e.g. 2025-10-15
            $table->date('period_end');     // e.g. 2025-11-14 (30-day period example)
            $table->date('due_date')->nullable();

            // amount locked from agreement
            $table->decimal('amount_due', 10, 2);

            // current balance/unpaid (initially = amount_due)
            $table->decimal('balance', 10, 2);

            $table->enum('status', ['unpaid','partial','paid'])->default('unpaid');

            // optional: notes
            $table->text('notes')->nullable();

            $table->timestamps();

            // prevent creating duplicate bill for same agreement + same period
            $table->unique(['agreement_id', 'period_start', 'period_end'], 'bills_unique_agreement_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
