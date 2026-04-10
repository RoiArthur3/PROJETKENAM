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
        Schema::create('inspection_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained('inspections')->onDelete('cascade');
            $table->unsignedBigInteger('checklist_item_id')->nullable(); // Changé pour retirer la contrainte

            // Détails de l'élément
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->enum('type', ['boolean', 'numeric', 'text', 'select', 'file']);
            $table->json('options')->nullable(); // Pour les champs de type select

            // Valeur de l'inspection
            $table->string('value')->nullable();
            $table->enum('status', ['pass', 'fail', 'n/a'])->nullable();
            $table->text('notes')->nullable();

            // Fichiers joints (photos, vidéos, etc.)
            $table->json('attachments')->nullable();

            // Métadonnées
            $table->integer('order')->default(0);
            $table->boolean('is_critical')->default(false);
            $table->boolean('requires_comment_on_fail')->default(true);

            // Horodatages
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspection_items');
    }
};
