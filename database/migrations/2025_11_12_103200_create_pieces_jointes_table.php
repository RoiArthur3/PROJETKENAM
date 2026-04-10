<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pieces_jointes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requete_id')->constrained('requetes')->onDelete('cascade');
            $table->string('nom_fichier');
            $table->string('chemin_fichier');
            $table->string('type_mime');
            $table->integer('taille');
            $table->foreignId('upload_par')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['requete_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pieces_jointes');
    }
};
