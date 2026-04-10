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
        Schema::table('caisses', function (Blueprint $table) {
            // Ajout des colonnes manquantes
            if (!Schema::hasColumn('caisses', 'code')) {
                $table->string('code', 20)->unique()->nullable()->after('id');
            }

            if (!Schema::hasColumn('caisses', 'libelle')) {
                $table->string('libelle')->after('code');
            }

            if (!Schema::hasColumn('caisses', 'description')) {
                $table->text('description')->nullable()->after('libelle');
            }

            if (!Schema::hasColumn('caisses', 'solde_initial')) {
                $table->decimal('solde_initial', 15, 2)->default(0)->after('description');
            }

            if (!Schema::hasColumn('caisses', 'solde_actuel')) {
                $table->decimal('solde_actuel', 15, 2)->default(0)->after('solde_initial');
            }

            if (!Schema::hasColumn('caisses', 'est_principale')) {
                $table->boolean('est_principale')->default(false)->after('solde_actuel');
            }

            if (!Schema::hasColumn('caisses', 'est_active')) {
                $table->boolean('est_active')->default(true)->after('est_principale');
            }

            if (!Schema::hasColumn('caisses', 'responsable_id')) {
                $table->foreignId('responsable_id')->nullable()->constrained('users')->onDelete('set null')->after('est_active');
            }

            if (!Schema::hasColumn('caisses', 'devise')) {
                $table->string('devise', 3)->default('XOF')->after('responsable_id');
            }

            if (!Schema::hasColumn('caisses', 'adresse')) {
                $table->text('adresse')->nullable()->after('devise');
            }

            if (!Schema::hasColumn('caisses', 'telephone')) {
                $table->string('telephone', 20)->nullable()->after('adresse');
            }

            if (!Schema::hasColumn('caisses', 'email')) {
                $table->string('email', 100)->nullable()->after('telephone');
            }

            if (!Schema::hasColumn('caisses', 'preferences')) {
                $table->text('preferences')->nullable()->after('email');
            }

            // Ajout de la suppression logique si elle n'existe pas
            if (!Schema::hasColumn('caisses', 'deleted_at')) {
                $table->softDeletes();
            }

            // Ajout des index si nécessaire
            if (!Schema::hasIndex('caisses', ['est_principale', 'est_active'])) {
                $table->index(['est_principale', 'est_active']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ne supprimez pas la table dans la méthode down pour éviter des problèmes
        // Nous allons simplement supprimer les colonnes ajoutées
        Schema::table('caisses', function (Blueprint $table) {
            $columnsToDrop = [
                'code', 'libelle', 'description', 'solde_initial', 'solde_actuel',
                'est_principale', 'est_active', 'responsable_id', 'devise', 'adresse',
                'telephone', 'email', 'preferences'
            ];

            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('caisses', $column)) {
                    $table->dropColumn($column);
                }
            }

            // Suppression de la suppression logique si elle a été ajoutée
            if (Schema::hasColumn('caisses', 'deleted_at')) {
                $table->dropSoftDeletes();
            }

            // Suppression des index si nécessaire
            $indexes = ['caisses_est_principale_est_active_index'];
            foreach ($indexes as $index) {
                if (Schema::hasIndex('caisses', $index)) {
                    $table->dropIndex($index);
                }
            }
        });
    }
};
