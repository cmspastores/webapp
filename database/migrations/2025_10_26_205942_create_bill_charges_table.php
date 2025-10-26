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
        Schema::create('bill_charges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bill_id')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable(); // optional: who added it
            $table->timestamps();

            $table->foreign('bill_id')->references('id')->on('bills')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_charges');
    }
};
