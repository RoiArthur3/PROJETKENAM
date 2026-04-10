<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('personnel', function (Blueprint $table) {
            // Augmenter la taille des champs textes pour éviter "string data, right truncated"
            if (Schema::hasColumn('personnel', 'nom')) {
                $table->string('nom', 255)->change();
            }

            if (Schema::hasColumn('personnel', 'prenoms')) {
                $table->string('prenoms', 255)->change();
            }

            if (Schema::hasColumn('personnel', 'email_personnel')) {
                $table->string('email_personnel', 255)->nullable()->change();
            }

            if (Schema::hasColumn('personnel', 'telephone_principal')) {
                $table->string('telephone_principal', 50)->change();
            }

            if (Schema::hasColumn('personnel', 'adresse_residence')) {
                $table->text('adresse_residence')->change();
            }

            if (Schema::hasColumn('personnel', 'ville')) {
                $table->string('ville', 100)->change();
            }

            if (Schema::hasColumn('personnel', 'matricule')) {
                $table->string('matricule', 50)->change();
            }

            if (Schema::hasColumn('personnel', 'poste')) {
                $table->string('poste', 255)->change();
            }

            if (Schema::hasColumn('personnel', 'departement')) {
                $table->string('departement', 255)->nullable()->change();
            }

            if (Schema::hasColumn('personnel', 'observations')) {
                $table->text('observations')->nullable()->change();
            }

            // Ajouter les colonnes manquantes si elles n'existent pas
            if (!Schema::hasColumn('personnel', 'code_badge')) {
                $table->string('code_badge', 50)->nullable();
            }

            if (!Schema::hasColumn('personnel', 'observations')) {
                $table->text('observations')->nullable();
            }
        });

        // Forcer l'encodage UTF-8 pour les caractères spéciaux
        DB::statement("ALTER TABLE personnel CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personnel', function (Blueprint $table) {
            // Revenir aux tailles d'origine si nécessaire
            if (Schema::hasColumn('personnel', 'nom')) {
                $table->string('nom', 100)->change();
            }

            if (Schema::hasColumn('personnel', 'prenoms')) {
                $table->string('prenoms', 150)->change();
            }

            if (Schema::hasColumn('personnel', 'email_personnel')) {
                $table->string('email_personnel', 100)->nullable()->change();
            }

            if (Schema::hasColumn('personnel', 'telephone_principal')) {
                $table->string('telephone_principal', 20)->change();
            }

            if (Schema::hasColumn('personnel', 'adresse_residence')) {
                $table->string('adresse_residence', 255)->change();
            }

            if (Schema::hasColumn('personnel', 'ville')) {
                $table->string('ville', 50)->change();
            }

            if (Schema::hasColumn('personnel', 'code_badge')) {
                $table->string('code_badge', 50)->nullable()->change();
            }

            if (Schema::hasColumn('personnel', 'matricule')) {
                $table->string('matricule', 20)->change();
            }

            if (Schema::hasColumn('personnel', 'poste')) {
                $table->string('poste', 100)->change();
            }

            if (Schema::hasColumn('personnel', 'departement')) {
                $table->string('departement', 100)->nullable()->change();
            }

            if (Schema::hasColumn('personnel', 'observations')) {
                $table->string('observations', 1000)->nullable()->change();
            }
        });
    }
};
