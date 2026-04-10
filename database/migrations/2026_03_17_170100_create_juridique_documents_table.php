<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('juridique_documents')) {
            return;
        }

        Schema::create('juridique_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrat_id')->nullable()->constrained('juridique_contrats')->nullOnDelete();
            $table->string('reference')->nullable()->unique();
            $table->string('titre');
            $table->string('type_document')->nullable();
            $table->string('chemin_fichier')->nullable();
            $table->date('date_document')->nullable();
            $table->date('date_expiration')->nullable();
            $table->string('statut')->default('actif');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('juridique_documents');
    }
};
