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
        Schema::table('trades', function (Blueprint $table) {
            $table->foreignId('asset_id')->nullable()->constrained('assets')->onDelete('SET NULL');
        });

        // Data Migration: Move existing string assets to assets table and link them
        $nonSyntheticType = DB::table('asset_types')->where('name', 'NON-SYNTHETIC')->first();
        if ($nonSyntheticType) {
            $trades = DB::table('trades')->whereNotNull('asset')->get();
            foreach ($trades as $trade) {
                // Find or create asset
                $assetId = DB::table('assets')->updateOrInsert(
                    ['user_id' => $trade->user_id, 'name' => $trade->asset, 'asset_type_id' => $nonSyntheticType->id],
                    ['created_at' => now(), 'updated_at' => now()]
                );
                
                // Get the ID (updateOrInsert doesn't return ID unfortunately)
                $asset = DB::table('assets')
                    ->where('user_id', $trade->user_id)
                    ->where('name', $trade->asset)
                    ->first();

                if ($asset) {
                    DB::table('trades')->where('id', $trade->id)->update(['asset_id' => $asset->id]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            $table->dropForeign(['asset_id']);
            $table->dropColumn('asset_id');
        });
    }
};
