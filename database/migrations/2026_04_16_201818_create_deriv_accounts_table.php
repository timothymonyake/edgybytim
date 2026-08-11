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
        Schema::create('deriv_accounts', function (Blueprint $col) {
            $col->id();
            $col->foreignId('user_id')->constrained()->cascadeOnDelete();
            $col->text('api_token'); // Encrypted
            $col->string('account_id')->unique();
            $col->decimal('balance', 15, 2)->default(0);
            $col->decimal('equity', 15, 2)->default(0);
            $col->string('currency')->default('USD');
            $col->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deriv_accounts');
    }
};
