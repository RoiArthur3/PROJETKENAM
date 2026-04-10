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
        Schema::table('depense_caisses', function (Blueprint $table) {
            $table->foreignId('operation_id')->nullable()->after('id')->constrained('operations')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('depense_caisses', function (Blueprint $table) {
            if (Schema::hasColumn('depense_caisses', 'operation_id')) {
                try {
                    $table->dropForeign(['operation_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist, continue
                }
                $table->dropColumn('operation_id');
            }
        });
    }
};
