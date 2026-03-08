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
        Schema::table('reminders', function (Blueprint $table) {
            $table->dateTime('remind_at')->nullable()->after('content');
            $table->enum('frequency', ['once', 'daily', 'weekly', 'custom'])->default('once')->after('remind_at');
            $table->json('recurrence_days')->nullable()->after('frequency');
            $table->boolean('is_active')->default(true)->after('recurrence_days');
            $table->dateTime('last_reminded_at')->nullable()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            $table->dropColumn(['remind_at', 'frequency', 'recurrence_days', 'is_active', 'last_reminded_at']);
        });
    }
};
