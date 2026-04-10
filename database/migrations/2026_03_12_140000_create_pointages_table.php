<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pointages')) {
            return;
        }

        Schema::create('pointages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operation_id')->constrained('operations')->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained('vehicules')->cascadeOnDelete();
            $table->foreignId('driver_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('bon_commande_id')->nullable()->constrained('bon_commandes')->nullOnDelete();
            
            $table->date('date_pointage');
            $table->time('heure_debut')->nullable();
            $table->time('heure_fin')->nullable();
            $table->decimal('duree_heures', 5, 2)->default(0);
            $table->decimal('duree_jours', 5, 2)->default(0);
            
            $table->boolean('is_retard')->default(false);
            $table->text('motif_retard')->nullable();
            
            $table->decimal('kilometrage_debut', 10, 2)->nullable();
            $table->decimal('kilometrage_fin', 10, 2)->nullable();
            $table->decimal('kilometrage_jour', 10, 2)->default(0);
            
            $table->decimal('carburant_debut', 8, 2)->nullable();
            $table->decimal('carburant_fin', 8, 2)->nullable();
            $table->decimal('carburant_consomme', 8, 2)->default(0);
            
            $table->enum('statut', ['en_cours', 'termine', 'valide', 'rejete'])->default('en_cours');
            $table->text('notes')->nullable();
            $table->text('observations')->nullable();
            
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();
            
            $table->decimal('efficacite_score', 5, 2)->default(0); // Score d'efficacité
            $table->decimal('objectif_heures', 5, 2)->default(0); // Objectif heures/jour
            $table->decimal('objectif_jours', 5, 2)->default(0); // Objectif jours total
            
            $table->timestamps();
            
            $table->index(['operation_id', 'date_pointage']);
            $table->index(['vehicle_id', 'date_pointage']);
            $table->index(['driver_id', 'date_pointage']);
            $table->index(['statut', 'date_pointage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pointages');
    }
};
