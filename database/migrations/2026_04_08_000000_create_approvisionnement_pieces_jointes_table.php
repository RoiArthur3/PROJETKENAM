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
        Schema::create('approvisionnement_pieces_jointes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approvisionnement_id')->constrained('approvisionnement_caisses')->onDelete('cascade');
            $table->string('nom_original');
            $table->string('chemin');
            $table->string('mime_type')->nullable();
            $table->integer('taille')->nullable();
            $table->foreignId('upload_par')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index('approvisionnement_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approvisionnement_pieces_jointes');
    }
};
