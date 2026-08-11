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
        Schema::create('deriv_daily_performance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deriv_account_id')->constrained('deriv_accounts')->cascadeOnDelete();
            $table->date('date');
            $table->decimal('starting_balance', 15, 2);
            $table->decimal('ending_balance', 15, 2);
            $table->decimal('pnl', 15, 2);
            $table->timestamps();

            $table->unique(['deriv_account_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deriv_daily_performance');
    }
};
