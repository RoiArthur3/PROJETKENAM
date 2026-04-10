<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'date_paiement')) {
                $table->timestamp('date_paiement')->nullable()->after('date_approbation');
                $table->index('date_paiement');
            }

            if (!Schema::hasColumn('expenses', 'payee_par')) {
                $table->foreignId('payee_par')->nullable()->after('date_paiement')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('expenses', 'reference_paiement')) {
                $table->string('reference_paiement', 100)->nullable()->after('payee_par');
            }
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'payee_par')) {
                $table->dropForeign(['payee_par']);
                $table->dropColumn('payee_par');
            }

            if (Schema::hasColumn('expenses', 'reference_paiement')) {
                $table->dropColumn('reference_paiement');
            }

            if (Schema::hasColumn('expenses', 'date_paiement')) {
                $table->dropIndex(['date_paiement']);
                $table->dropColumn('date_paiement');
            }
        });
    }
};
