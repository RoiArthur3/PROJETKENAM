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
        Schema::create('controles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicule_id')->constrained('vehicles')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->date('date_controle');
            $table->string('type_controle');
            $table->text('commentaires')->nullable();
            $table->enum('resultat', ['conforme', 'non_conforme', 'en_attente'])->default('en_attente');
            $table->json('details')->nullable();
            $table->timestamps();
            
            // Index pour les recherches fréquentes
            $table->index(['date_controle', 'type_controle', 'resultat']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('controles');
    }
};
