<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discipline_daily_analyses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('analysis_date');
            $table->enum('phase', ['premarket', 'market', 'postmarket']);
            $table->string('chart_url')->nullable();           // TradingView snapshot URL
            $table->string('chart_preview_url')->nullable();  // Derived image URL for preview
            $table->string('timeframe')->nullable();           // e.g. 1H, 4H, D
            $table->string('symbol')->nullable();              // e.g. XAUUSD
            $table->string('bias')->nullable();                // Bullish / Bearish / Neutral
            $table->json('key_levels')->nullable();            // array of price levels noted
            $table->text('narrative')->nullable();             // main analysis text / note
            $table->string('forex_week_url')->nullable();      // FF calendar URL
            $table->integer('sort_order')->default(0);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'analysis_date', 'phase', 'sort_order'], 'dda_user_date_phase_order_unique');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discipline_daily_analyses');
    }
};
