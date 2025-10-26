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
        // MySQL/Postgres: change column type
        if (config('database.default') !== 'sqlite') {
            Schema::table('bills', function (Blueprint $table) {
                $table->dateTime('due_date')->nullable()->change();
            });
            return;
        }

        // SQLite fallback: add new column, copy, drop old, rename
        Schema::table('bills', function (Blueprint $table) {
            $table->dateTime('due_date_tmp')->nullable()->after('period_end');
        });
        // copy old date values into new column (as midnight) - raw query
        \DB::statement("UPDATE bills SET due_date_tmp = due_date");
        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn('due_date');
        });
        Schema::table('bills', function (Blueprint $table) {
            $table->renameColumn('due_date_tmp', 'due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (config('database.default') !== 'sqlite') {
            Schema::table('bills', function (Blueprint $table) {
                $table->date('due_date')->nullable()->change();
            });
            return;
        }

        Schema::table('bills', function (Blueprint $table) {
            $table->date('due_date_tmp')->nullable()->after('period_end');
        });
        \DB::statement("UPDATE bills SET due_date_tmp = due_date");
        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn('due_date');
        });
        Schema::table('bills', function (Blueprint $table) {
            $table->renameColumn('due_date_tmp', 'due_date');
        });
    }
};
