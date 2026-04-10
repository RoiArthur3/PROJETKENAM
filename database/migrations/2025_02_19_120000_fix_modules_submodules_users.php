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
        Schema::table('users', function (Blueprint $table) {
            // Ajouter la colonne modules si elle n'existe pas
            if (!Schema::hasColumn('users', 'modules')) {
                $table->json('modules')->nullable()->comment('Modules autorisés pour les modérateurs');
            }
            
            // Ajouter la colonne submodules si elle n'existe pas
            if (!Schema::hasColumn('users', 'submodules')) {
                $table->json('submodules')->nullable()->comment('Sous-modules autorisés pour les modérateurs');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'submodules')) {
                $table->dropColumn('submodules');
            }
            if (Schema::hasColumn('users', 'modules')) {
                $table->dropColumn('modules');
            }
        });
    }
};
