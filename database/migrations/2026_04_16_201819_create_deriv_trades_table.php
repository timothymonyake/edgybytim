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
        Schema::create('deriv_trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deriv_account_id')->constrained('deriv_accounts')->cascadeOnDelete();
            $table->string('symbol');
            $table->string('contract_type'); // BUY/SELL
            $table->decimal('entry_price', 15, 5);
            $table->decimal('exit_price', 15, 5)->nullable();
            $table->decimal('profit_loss', 15, 2)->default(0);
            $table->decimal('stake', 15, 2);
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->json('raw_payload')->nullable();
            $table->text('notes')->nullable(); // For journaling
            $table->json('tags')->nullable(); // Strategy/session
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deriv_trades');
    }
};
