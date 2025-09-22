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
        Schema::create('trade_screenshots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trade_id');
            $table->enum('when',['before','during','after']);
            $table->string('url');
            $table->text('notes');
            $table->foreign('trade_id')->references('id')->on('trades')->onDelete('cascade');
            $table->timestamps();


            /* $table->enum('type', ['daily_chart', 'hourly_chart', 'entry_chart', 'after_chart', 'other']);
            $table->string('url'); *///trading_view
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trade_screenshots');
    }
};
