<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // D'abord ajouter les colonnes si elles n'existent pas
            if (!Schema::hasColumn('users', 'can_access_dashboard')) {
                $table->boolean('can_access_dashboard')->default(false);
            }
            if (!Schema::hasColumn('users', 'can_access_projects')) {
                $table->boolean('can_access_projects')->default(false);
            }
            if (!Schema::hasColumn('users', 'can_access_audit')) {
                $table->boolean('can_access_audit')->default(false);
            }
            if (!Schema::hasColumn('users', 'can_access_reporting')) {
                $table->boolean('can_access_reporting')->default(false);
            }
            // Modification au lieu d'ajout
            $table->boolean('can_access_dashboard')->default(false)->change();
            $table->boolean('can_access_projects')->default(false)->change();
            $table->boolean('can_access_audit')->default(false)->change();
            $table->boolean('can_access_reporting')->default(false)->change();
        });
    }

    public function down(): void
    {
        // Optionnel : Revertir les valeurs par défaut si nécessaire
    }
};
