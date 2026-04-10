<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Informations personnelles (Code du travail Ivoirien)
            if (!Schema::hasColumn('users', 'sexe')) {
                $table->string('sexe', 10)->nullable();
            }
            if (!Schema::hasColumn('users', 'date_naissance')) {
                $table->date('date_naissance')->nullable();
            }
            if (!Schema::hasColumn('users', 'lieu_naissance')) {
                $table->string('lieu_naissance')->nullable();
            }
            if (!Schema::hasColumn('users', 'nationalite')) {
                $table->string('nationalite')->nullable();
            }
            if (!Schema::hasColumn('users', 'situation_matrimoniale')) {
                $table->string('situation_matrimoniale')->nullable();
            }
            if (!Schema::hasColumn('users', 'nombre_enfants')) {
                $table->integer('nombre_enfants')->default(0);
            }
            if (!Schema::hasColumn('users', 'adresse_postale')) {
                $table->string('adresse_postale')->nullable();
            }
            
            // Numéros sociaux
            if (!Schema::hasColumn('users', 'n_cnps')) {
                $table->string('n_cnps')->nullable();
            }
            if (!Schema::hasColumn('users', 'n_cmu')) {
                $table->string('n_cmu')->nullable();
            }
            
            // Détails du salaire (Éléments constitutifs)
            if (!Schema::hasColumn('users', 'salaire_base')) {
                $table->decimal('salaire_base', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('users', 'sursalaire')) {
                $table->decimal('sursalaire', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('users', 'indemnite_transport')) {
                $table->decimal('indemnite_transport', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('users', 'indemnite_logement')) {
                $table->decimal('indemnite_logement', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('users', 'autres_primes')) {
                $table->decimal('autres_primes', 12, 2)->nullable();
            }
            
            // Classification professionnelle
            if (!Schema::hasColumn('users', 'categorie_professionnelle')) {
                $table->string('categorie_professionnelle')->nullable();
            }
            
            // Détails supplémentaires du contrat
            if (!Schema::hasColumn('users', 'date_fin_contrat')) {
                $table->date('date_fin_contrat')->nullable();
            }
            if (!Schema::hasColumn('users', 'periode_essai')) {
                $table->integer('periode_essai')->nullable(); // en jours
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'sexe', 'date_naissance', 'lieu_naissance', 'nationalite',
                'situation_matrimoniale', 'nombre_enfants', 'adresse_postale',
                'n_cnps', 'n_cmu', 'salaire_base', 'sursalaire',
                'indemnite_transport', 'indemnite_logement', 'autres_primes',
                'categorie_professionnelle', 'date_fin_contrat', 'periode_essai'
            ]);
        });
    }
};
