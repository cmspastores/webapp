<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_items', function (Blueprint $table) {
            $table->id();
            // Link to payments.billing_id (teammate PK)
            $table->unsignedBigInteger('payment_id');
            $table->unsignedBigInteger('bill_id');
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamps();

            // FKs
            $table->foreign('payment_id')->references('billing_id')->on('payments')->onDelete('cascade');
            $table->foreign('bill_id')->references('id')->on('bills')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_items');
    }
};
