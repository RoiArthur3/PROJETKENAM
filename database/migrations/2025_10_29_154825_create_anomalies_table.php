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
        Schema::create('anomalies', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            
            // Liens vers l'inspection et l'élément concerné
            $table->foreignId('inspection_id')->constrained('inspections')->onDelete('cascade');
            $table->foreignId('inspection_item_id')->nullable()->constrained('inspection_items')->onDelete('set null');
            $table->morphs('anomalizable'); // Pour lier à différents modèles (véhicule, équipement, etc.)
            
            // Détails de l'anomalie
            $table->string('title');
            $table->text('description');
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'rejected', 'closed'])->default('open');
            $table->enum('source', ['inspection', 'maintenance', 'user_report', 'system'])->default('inspection');
            
            // Responsable et dates
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->foreignId('reported_by')->constrained('users');
            $table->dateTime('detected_at');
            $table->dateTime('resolved_at')->nullable();
            $table->dateTime('target_resolution_date')->nullable();
            
            // Classification
            $table->string('category')->nullable();
            $table->string('subcategory')->nullable();
            $table->json('tags')->nullable();
            
            // Impact et urgence
            $table->integer('impact')->nullable()->comment('1-5 scale');
            $table->integer('urgency')->nullable()->comment('1-5 scale');
            $table->integer('priority')->virtualAs('COALESCE(impact, 3) * COALESCE(urgency, 3)');
            
            // Données techniques
            $table->json('technical_data')->nullable();
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
        Schema::dropIfExists('anomalies');
    }
};
