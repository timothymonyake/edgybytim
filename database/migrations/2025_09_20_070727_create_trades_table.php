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
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->string('asset');
            $table->enum('direction', ['long', 'short']);
            //$table->enum('trade_type', ['scalp', 'intraday', 'swing'])->nullable();
            $table->date('trade_date');
            $table->string('setup')->nullable();
            $table->enum('status',['open','closed'])->default('open');
            $table->string('session')->nullable();
            $table->json('entry_pd_array')->nullable();
            $table->string('daily_log_url')->nullable();
            $table->enum('entry_type', ['market', 'limit', 'stop'])->nullable();

            //$table->decimal('lot_size', 10, 2)->nullable();
            //$table->decimal('risk_percent', 5, 2)->nullable();
            $table->decimal('rr', 5, 2)->default(0.0);
            $table->decimal('pips', 8, 2)->nullable();
            $table->enum('outcome', ['pending','win', 'loss', 'breakeven'])->nullable();
            $table->boolean('plan_followed')->default(true);
            $table->string('news')->nullable();
            $table->string('emotions')->nullable();
            $table->string('entry_narrative')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};
