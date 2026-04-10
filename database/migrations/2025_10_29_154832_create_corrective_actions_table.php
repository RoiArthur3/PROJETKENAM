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
        Schema::create('corrective_actions', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            
            // Liens vers l'anomalie et l'élément concerné
            $table->foreignId('anomaly_id')->constrained('anomalies')->onDelete('cascade');
            $table->foreignId('inspection_id')->nullable()->constrained('inspections')->onDelete('set null');
            $table->morphs('actionable'); // Pour lier à différents modèles (maintenance, achat, etc.)
            
            // Détails de l'action corrective
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['repair', 'replacement', 'adjustment', 'cleaning', 'other']);
            $table->enum('status', ['pending', 'approved', 'in_progress', 'completed', 'cancelled'])->default('pending');
            
            // Planification
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->dateTime('scheduled_start_date')->nullable();
            $table->dateTime('scheduled_end_date')->nullable();
            
            // Exécution
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->integer('time_spent_minutes')->nullable();
            
            // Coûts
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->decimal('actual_cost', 10, 2)->nullable();
            
            // Pièces utilisées (liens vers le module stock)
            $table->json('used_parts')->nullable();
            
            // Résultats
            $table->text('actions_taken')->nullable();
            $table->text('results')->nullable();
            $table->boolean('is_effective')->nullable();
            
            // Validation
            $table->foreignId('validated_by')->nullable()->constrained('users');
            $table->dateTime('validated_at')->nullable();
            $table->text('validation_notes')->nullable();
            
            // Pièces jointes
            $table->json('attachments')->nullable();
            
            // Suivi
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('corrective_actions');
    }
};
