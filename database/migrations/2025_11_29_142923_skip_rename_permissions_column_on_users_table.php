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
        // Ne rien faire, cette migration est destinée à sauter la migration problématique
        // qui tente de renommer la colonne permissions en legacy_permissions

        // Vérifier si la colonne permissions existe toujours
        if (Schema::hasColumn('users', 'permissions')) {
            // Si oui, la supprimer car elle n'est plus nécessaire avec Spatie Permissions
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('permissions');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // En cas de rollback, recréer la colonne permissions si nécessaire
        if (!Schema::hasColumn('users', 'permissions')) {
            Schema::table('users', function (Blueprint $table) {
                $table->json('permissions')->nullable()->after('role');
            });
        }
    }
};
