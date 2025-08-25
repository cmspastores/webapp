<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('roomtypes', function (Blueprint $table) {
<<<<<<<< HEAD:database/migrations/2025_08_04_202706_create_roomtypes_table.php
            $table->id();
========
            $table->id()->primary();
>>>>>>>> 87780e9e574982e816c803ddc8071db391955004:database/migrations/2025_08_25_090019_create_roomtypes_table.php
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