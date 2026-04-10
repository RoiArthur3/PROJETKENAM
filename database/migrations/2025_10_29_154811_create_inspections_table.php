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
        if (!Schema::hasTable('inspections')) {
            Schema::create('inspections', function (Blueprint $table) {
                $table->id();
                $table->string('reference')->unique();
                $table->foreignId('checking_id')->constrained('checkings');
                $table->foreignId('checklist_id')->constrained('inspection_checklists');
                $table->morphs('inspectable'); // Pour lier à différents modèles (véhicule, équipement, etc.)
                $table->foreignId('inspector_id')->constrained('users');
                
                // Informations sur l'inspection
                $table->dateTime('started_at');
                $table->dateTime('completed_at')->nullable();
                $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
                
                // Résultats globaux
                $table->enum('result', ['conform', 'non_conform', 'conform_with_reserves'])->nullable();
                $table->integer('total_items')->default(0);
                $table->integer('passed_items')->default(0);
                $table->integer('failed_items')->default(0);
                $table->integer('items_with_reserves')->default(0);
                
                // Métadonnées
                $table->text('notes')->nullable();
                $table->json('custom_fields')->nullable();
                
                // Signature électronique
                $table->text('signature_data')->nullable();
                $table->timestamp('signed_at')->nullable();
                
                // Gestion des versions et suppression
                $table->foreignId('created_by')->constrained('users');
                $table->foreignId('updated_by')->nullable()->constrained('users');
                $table->softDeletes();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
