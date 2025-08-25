<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('roomtypes', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('title');
            $table->text('description')->nullable();
            // $table->boolean('completed')->default(false);
            $table->string('status')->default('Pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roomtypes');
    }
};