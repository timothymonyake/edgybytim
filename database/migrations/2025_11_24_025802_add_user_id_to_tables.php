<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = ['trades', 'rules', 'psychological_tips', 'entry_checklists'];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('cascade');
                });
                
                // Assign existing records to user ID 1 (Segolame)
                DB::table($table)->update(['user_id' => 1]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['trades', 'rules', 'psychological_tips', 'entry_checklists'];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropForeign(['user_id']);
                    $table->dropColumn('user_id');
                });
            }
        }
    }
};
