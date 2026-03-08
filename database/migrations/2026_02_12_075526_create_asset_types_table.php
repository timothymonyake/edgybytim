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
        Schema::create('asset_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., SYNTHETIC, NON-SYNTHETIC
            $table->timestamps();
        });

        // Seed default types
        DB::table('asset_types')->insert([
            ['name' => 'SYNTHETIC', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'NON-SYNTHETIC', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_types');
    }
};
