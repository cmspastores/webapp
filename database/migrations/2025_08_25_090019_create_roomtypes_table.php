<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('roomtypes', function (Blueprint $table) {
<<<<<<< HEAD:database/migrations/2025_08_04_202706_create_roomtypes_table.php
            $table->id()->primary();
=======

            $table->id();

            $table->id()->primary();

>>>>>>> d7834bd53a3f2fd8cd0b3311f6371f8da5a774d1:database/migrations/2025_08_25_090019_create_roomtypes_table.php
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