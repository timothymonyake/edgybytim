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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('broker');
            $table->string('account_number')->nullable();
            $table->decimal('account_size', 15, 2);
            $table->string('currency')->default('USD');
            $table->string('platform')->nullable();
            $table->string('phase')->default('Challenge'); // Challenge, Verification, Funded, Personal, Demo
            $table->string('status')->default('Active'); // Active, Passed, Failed, Archived
            $table->decimal('initial_balance', 15, 2);
            $table->decimal('current_balance', 15, 2)->nullable();
            $table->decimal('profit_target', 15, 2)->nullable();
            $table->decimal('max_daily_loss', 15, 2)->nullable();
            $table->decimal('max_total_loss', 15, 2)->nullable();
            $table->string('leverage')->nullable();
            $table->string('timezone')->nullable();
            $table->string('color')->default('#3b82f6');
            $table->text('notes')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
