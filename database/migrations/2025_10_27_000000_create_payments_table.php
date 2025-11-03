<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            // Billing ID (primary key)
            $table->id('billing_id');

            // Foreign key to agreements.agreement_id
            $table->unsignedBigInteger('agreement_id')->nullable();
            $table->foreign('agreement_id')
                  ->references('agreement_id')
                  ->on('agreements')
                  ->onDelete('cascade');

            // Link to a specific bill (nullable)
            $table->unsignedBigInteger('bill_id')->nullable();

            // Who paid (visible on record)
            $table->string('payer_name')->nullable();

            // Amount and payment date
            $table->decimal('amount', 12, 2);
            $table->dateTime('payment_date');

            // Extra bookkeeping
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();

            // In case an overpayment cannot be allocated immediately (we store leftover)
            $table->decimal('unallocated_amount', 12, 2)->default(0);

            $table->timestamps();

            // Indexes & FKs (bill_id -> bills.id)
            $table->index('agreement_id');
            $table->index('bill_id');

            // Add FK to bills if you are confident bills table exists before migration runs.
            // If you have migration order such that bills exist first, enable the FK:
            $table->foreign('bill_id')->references('id')->on('bills')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};