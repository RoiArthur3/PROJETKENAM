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
        Schema::create('operation_staff', function (Blueprint $table) {
            $table->id();
            
            // Relations
            $table->foreignId('operation_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Rôle dans l'opération
            $table->enum('role', ['chauffeur', 'assistant', 'controleur', 'agent_logistique', 'superviseur'])->default('agent_logistique');
            
            // Suivi temporel
            $table->datetime('assigned_at');
            $table->datetime('started_at')->nullable();
            $table->datetime('completed_at')->nullable();
            
            // Heures travaillées
            $table->decimal('hours_worked', 5, 2)->nullable();
            $table->decimal('overtime_hours', 5, 2)->nullable();
            
            // Statut
            $table->enum('status', ['assigned', 'active', 'completed', 'absent'])->default('assigned');
            
            // Notes
            $table->text('notes')->nullable();
            $table->text('performance_notes')->nullable();
            
            $table->timestamps();
            
            // Index + contrainte unique
            $table->unique(['operation_id', 'user_id']);
            $table->index(['operation_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operation_staff');
    }
};
