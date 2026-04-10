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
        Schema::create('checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checklist_id')->constrained('inspection_checklists')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('checklist_items')->onDelete('cascade');
            
            // Détails de l'élément
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['section', 'boolean', 'numeric', 'text', 'select', 'file'])->default('boolean');
            $table->json('options')->nullable(); // Pour les champs de type select
            $table->string('unit')->nullable(); // Unité de mesure pour les champs numériques
            
            // Valeurs par défaut et contraintes
            $table->string('default_value')->nullable();
            $table->string('min_value')->nullable();
            $table->string('max_value')->nullable();
            $table->boolean('is_required')->default(true);
            
            // Métadonnées
            $table->integer('order')->default(0);
            $table->boolean('is_critical')->default(false);
            $table->boolean('requires_comment_on_fail')->default(true);
            $table->boolean('requires_photo')->default(false);
            
            // Pour le suivi
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_items');
    }
};
