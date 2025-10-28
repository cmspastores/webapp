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
            $table->unsignedBigInteger('agreement_id');
            $table->foreign('agreement_id')
                  ->references('agreement_id')
                  ->on('agreements')
                  ->onDelete('cascade');

            // Amount and payment date
            $table->decimal('amount', 12, 2);
            $table->dateTime('payment_date');

            // Optional bookkeeping
            $table->string('reference')->nullable();
            $table->timestamps();

            // Index for faster lookups by agreement
            $table->index('agreement_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};