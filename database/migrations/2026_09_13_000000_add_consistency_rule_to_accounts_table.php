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
        Schema::table('accounts', function (Blueprint $table) {
            $table->boolean('has_consistency_rule')->default(false)->after('max_total_loss');
            $table->decimal('consistency_rule_percent', 5, 2)->nullable()->default(50.00)->after('has_consistency_rule');
            $table->string('consistency_rule_type')->default('day')->after('consistency_rule_percent'); // 'day' or 'trade'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn(['has_consistency_rule', 'consistency_rule_percent', 'consistency_rule_type']);
        });
    }
};
