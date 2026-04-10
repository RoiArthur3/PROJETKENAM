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
        Schema::table('invoices', function (Blueprint $table) {
            // Ajouter la relation inverse avec operations
            if (!Schema::hasColumn('invoices', 'operation_id')) {
                $table->foreignId('operation_id')->nullable()->after('client_id')->constrained('operations')->onDelete('set null');
                $table->index('operation_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'operation_id')) {
                $table->dropForeign(['operation_id']);
                $table->dropIndex(['operation_id']);
                $table->dropColumn('operation_id');
            }
        });
    }
};
