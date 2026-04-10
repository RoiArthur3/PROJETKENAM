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
        Schema::table('devis', function (Blueprint $table) {
            // Type de devis
            $table->string('type_devis')->default('vente')->after('objet');
            
            // Champs spécifiques location
            $table->integer('location_duree_jours')->nullable()->after('type_devis');
            $table->decimal('location_tarif_journalier_ht', 12, 2)->nullable()->after('location_duree_jours');
            $table->decimal('location_tarif_journalier_ttc', 12, 2)->nullable()->after('location_tarif_journalier_ht');
            $table->integer('location_nombre_vehicules')->nullable()->after('location_tarif_journalier_ttc');
            $table->string('location_type_vehicules')->nullable()->after('location_nombre_vehicules');
            $table->integer('location_kilometrage_inclus')->nullable()->after('location_type_vehicules');
            $table->decimal('location_frais_kilometrage_supplementaire', 8, 2)->nullable()->after('location_kilometrage_inclus');
            $table->decimal('location_caution', 12, 2)->nullable()->after('location_frais_kilometrage_supplementaire');
            $table->boolean('location_assurance_incluse')->default(false)->after('location_caution');
            $table->boolean('location_carburant_inclus')->default(false)->after('location_assurance_incluse');
            $table->boolean('location_chauffeur_inclus')->default(false)->after('location_carburant_inclus');
            
            // Dates de location
            $table->date('location_date_debut')->nullable()->after('location_chauffeur_inclus');
            $table->date('location_date_fin')->nullable()->after('location_date_debut');
            $table->datetime('location_heure_debut')->nullable()->after('location_date_fin');
            $table->datetime('location_heure_fin')->nullable()->after('location_heure_debut');
            
            // Coûts additionnels
            $table->decimal('frais_livraison_ht', 12, 2)->nullable()->after('location_heure_fin');
            $table->decimal('frais_livraison_ttc', 12, 2)->nullable()->after('frais_livraison_ht');
            $table->decimal('frais_mise_disposition_ht', 12, 2)->nullable()->after('frais_livraison_ttc');
            $table->decimal('frais_mise_disposition_ttc', 12, 2)->nullable()->after('frais_mise_disposition_ht');
            $table->decimal('frais_nettoyage_ht', 12, 2)->nullable()->after('frais_mise_disposition_ttc');
            $table->decimal('frais_nettoyage_ttc', 12, 2)->nullable()->after('frais_nettoyage_ht');
            
            // Validation et suivi
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null')->after('frais_nettoyage_ttc');
            $table->datetime('validated_at')->nullable()->after('validated_by');
            $table->foreignId('operation_id_associee')->nullable()->constrained()->onDelete('set null')->after('validated_at');
            
            // Indexes
            $table->index(['type_devis', 'statut']);
            $table->index(['location_date_debut', 'location_date_fin']);
            $table->index('validated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devis', function (Blueprint $table) {
            $table->dropColumn([
                'type_devis',
                'location_duree_jours',
                'location_tarif_journalier_ht',
                'location_tarif_journalier_ttc',
                'location_nombre_vehicules',
                'location_type_vehicules',
                'location_kilometrage_inclus',
                'location_frais_kilometrage_supplementaire',
                'location_caution',
                'location_assurance_incluse',
                'location_carburant_inclus',
                'location_chauffeur_inclus',
                'location_date_debut',
                'location_date_fin',
                'location_heure_debut',
                'location_heure_fin',
                'frais_livraison_ht',
                'frais_livraison_ttc',
                'frais_mise_disposition_ht',
                'frais_mise_disposition_ttc',
                'frais_nettoyage_ht',
                'frais_nettoyage_ttc',
                'validated_by',
                'validated_at',
                'operation_id_associee'
            ]);
        });
    }
};
