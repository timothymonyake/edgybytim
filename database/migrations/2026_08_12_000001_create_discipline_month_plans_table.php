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
        Schema::create('discipline_month_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('year_month', 7); // e.g. '2026-08'
            $table->string('background_image')->nullable();
            $table->json('layout_config')->nullable(); // quote texts, photo collage URLs, colors
            $table->json('monthly_activities')->nullable(); // list of defined routines/tasks
            $table->boolean('is_locked')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'year_month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discipline_month_plans');
    }
};
