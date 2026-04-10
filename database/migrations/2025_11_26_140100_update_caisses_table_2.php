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
            // Vérification et ajout des colonnes manquantes
            if (!Schema::hasColumn('caisses', 'code')) {
                $table->string('code', 20)->unique()->after('id');
            }

            if (!Schema::hasColumn('caisses', 'libelle')) {
                $table->string('libelle')->nullable()->after('code');
            }

            if (!Schema::hasColumn('caisses', 'est_principale')) {
                $table->boolean('est_principale')->default(false)->after('est_active');
            }

            if (!Schema::hasColumn('caisses', 'adresse')) {
                $table->text('adresse')->nullable()->after('responsable_id');
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

            // Renommer la colonne 'nom' en 'libelle' si elle existe
            if (Schema::hasColumn('caisses', 'nom') && !Schema::hasColumn('caisses', 'libelle')) {
                $table->renameColumn('nom', 'libelle');
            }

            // Ajout de l'index si nécessaire
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
        Schema::table('caisses', function (Blueprint $table) {
            // Suppression des colonnes ajoutées
            $columnsToDrop = [
                'code', 'libelle', 'est_principale',
                'adresse', 'telephone', 'email', 'preferences'
            ];

            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('caisses', $column)) {
                    $table->dropColumn($column);
                }
            }

            // Suppression de l'index
            if (Schema::hasIndex('caisses', ['est_principale', 'est_active'])) {
                $table->dropIndex(['est_principale', 'est_active']);
            }

            // Rétablir la colonne 'nom' si elle a été renommée
            if (!Schema::hasColumn('caisses', 'nom') && Schema::hasColumn('caisses', 'libelle')) {
                $table->renameColumn('libelle', 'nom');
            }
        });
    }
};
