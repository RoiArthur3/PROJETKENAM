<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('depense_caisses')) {
            return;
        }

        Schema::table('depense_caisses', function (Blueprint $table) {
            if (!Schema::hasColumn('depense_caisses', 'expense_id')) {
                $table->foreignId('expense_id')
                    ->nullable()
                    ->after('facture_fournisseur_id')
                    ->constrained('expenses')
                    ->nullOnDelete();

                $table->unique('expense_id');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('depense_caisses')) {
            return;
        }

        Schema::table('depense_caisses', function (Blueprint $table) {
            if (Schema::hasColumn('depense_caisses', 'expense_id')) {
                $table->dropForeign(['expense_id']);
                $table->dropUnique(['expense_id']);
                $table->dropColumn('expense_id');
            }
        });
    }
};
