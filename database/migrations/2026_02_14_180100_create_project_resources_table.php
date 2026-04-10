<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_resources', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->enum('type', ['vehicule', 'chauffeur', 'agent', 'materiel', 'autre']); // Type de ressource
            $table->unsignedBigInteger('resource_id'); // ID du véhicule, chauffeur, agent, ou matériel
            $table->integer('quantite')->default(1);
            $table->date('date_affectation');
            $table->date('date_liberation')->nullable();
            $table->text('notes')->nullable();
            $table->enum('statut', ['planifiee', 'active', 'liberee', 'suspendue'])->default('planifiee');
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_resources');
    }
};
