<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('commande_fournisseurs')) {
            return;
        }

        Schema::table('commande_fournisseurs', function (Blueprint $table) {
            if (!Schema::hasColumn('commande_fournisseurs', 'type_achat')) {
                $table->string('type_achat')->nullable()->after('fournisseur_id');
            }

            if (!Schema::hasColumn('commande_fournisseurs', 'service_concerne')) {
                $table->string('service_concerne')->nullable()->after('type_achat');
            }

            if (!Schema::hasColumn('commande_fournisseurs', 'compte_comptable_id')) {
                $table->foreignId('compte_comptable_id')->nullable()->after('service_concerne')->constrained('comptes_comptables')->nullOnDelete();
            }

            if (!Schema::hasColumn('commande_fournisseurs', 'expense_id')) {
                $table->foreignId('expense_id')->nullable()->after('compte_comptable_id')->constrained('expenses')->nullOnDelete();
            }

            if (!Schema::hasColumn('commande_fournisseurs', 'validated_by')) {
                $table->foreignId('validated_by')->nullable()->after('expense_id')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('commande_fournisseurs', 'date_validation_achat')) {
                $table->timestamp('date_validation_achat')->nullable()->after('validated_by');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('commande_fournisseurs')) {
            return;
        }

        Schema::table('commande_fournisseurs', function (Blueprint $table) {
            if (Schema::hasColumn('commande_fournisseurs', 'date_validation_achat')) {
                $table->dropColumn('date_validation_achat');
            }
            if (Schema::hasColumn('commande_fournisseurs', 'validated_by')) {
                $table->dropForeign(['validated_by']);
                $table->dropColumn('validated_by');
            }
            if (Schema::hasColumn('commande_fournisseurs', 'expense_id')) {
                $table->dropForeign(['expense_id']);
                $table->dropColumn('expense_id');
            }
            if (Schema::hasColumn('commande_fournisseurs', 'compte_comptable_id')) {
                $table->dropForeign(['compte_comptable_id']);
                $table->dropColumn('compte_comptable_id');
            }
            if (Schema::hasColumn('commande_fournisseurs', 'service_concerne')) {
                $table->dropColumn('service_concerne');
            }
            if (Schema::hasColumn('commande_fournisseurs', 'type_achat')) {
                $table->dropColumn('type_achat');
            }
        });
    }
};