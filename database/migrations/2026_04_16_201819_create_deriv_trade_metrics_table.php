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
        Schema::create('deriv_trade_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deriv_account_id')->unique()->constrained('deriv_accounts')->cascadeOnDelete();
            $table->decimal('win_rate', 5, 2)->default(0);
            $table->unsignedInteger('total_trades')->default(0);
            $table->unsignedInteger('wins')->default(0);
            $table->unsignedInteger('losses')->default(0);
            $table->unsignedInteger('breakeven')->default(0);
            $table->decimal('avg_win', 15, 2)->default(0);
            $table->decimal('avg_loss', 15, 2)->default(0);
            $table->decimal('risk_reward_ratio', 8, 2)->default(0);
            $table->decimal('profit_factor', 8, 2)->default(0);
            $table->decimal('expectancy', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deriv_trade_metrics');
    }
};
