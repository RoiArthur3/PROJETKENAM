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
        Schema::create('fichiers_operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operation_id')->constrained()->onDelete('cascade');
            $table->string('nom_original');
            $table->string('chemin');
            $table->string('type_fichier');
            $table->bigInteger('taille');
            $table->string('uploaded_by')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['operation_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fichiers_operations');
    }
};
