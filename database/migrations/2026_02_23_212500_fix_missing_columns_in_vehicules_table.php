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
        Schema::table('vehicules', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicules', 'annee')) {
                $table->integer('annee')->nullable()->after('modele');
            }
            if (!Schema::hasColumn('vehicules', 'couleur')) {
                $table->string('couleur', 50)->nullable()->after('annee');
            }
            if (!Schema::hasColumn('vehicules', 'kilometrage')) {
                $table->integer('kilometrage')->nullable()->after('couleur');
            }
            if (!Schema::hasColumn('vehicules', 'date_achat')) {
                $table->date('date_achat')->nullable()->after('kilometrage');
            }
            if (!Schema::hasColumn('vehicules', 'prix_achat')) {
                $table->decimal('prix_achat', 15, 2)->nullable()->after('date_achat');
            }
            if (!Schema::hasColumn('vehicules', 'carburant')) {
                $table->string('carburant', 50)->nullable()->after('prix_achat');
            }
            if (!Schema::hasColumn('vehicules', 'notes')) {
                $table->text('notes')->nullable()->after('carburant');
            }
            if (!Schema::hasColumn('vehicules', 'date_fin_assurance')) {
                $table->date('date_fin_assurance')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('vehicules', 'prix_location')) {
                $table->decimal('prix_location', 15, 2)->nullable()->after('disponible');
            }
            if (!Schema::hasColumn('vehicules', 'date_debut_contrat')) {
                $table->date('date_debut_contrat')->nullable()->after('prix_location');
            }
            if (!Schema::hasColumn('vehicules', 'provenance')) {
                $table->string('provenance', 50)->nullable()->after('date_debut_contrat');
            }
            if (!Schema::hasColumn('vehicules', 'fournisseur_id')) {
                $table->foreignId('fournisseur_id')->nullable()->after('provenance')->constrained('fournisseurs')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicules', function (Blueprint $table) {
            $table->dropForeign(['fournisseur_id']);
            $table->dropColumn([
                'annee', 'couleur', 'kilometrage', 'date_achat', 'prix_achat', 
                'carburant', 'notes', 'date_fin_assurance', 'prix_location', 
                'date_debut_contrat', 'provenance', 'fournisseur_id'
            ]);
        });
    }
};
