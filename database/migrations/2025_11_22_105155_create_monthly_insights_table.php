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
        Schema::create('monthly_insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('period'); // e.g., "2025-11" for November 2025
            $table->text('insight');
            $table->integer('trade_count')->default(0);
            $table->decimal('total_pnl', 10, 2)->default(0);
            $table->decimal('win_rate', 5, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['user_id', 'period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_insights');
    }
};
