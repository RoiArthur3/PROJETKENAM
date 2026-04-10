<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();

            $table->foreignId('vehicule_id')->nullable()->constrained('vehicules')->nullOnDelete();

            $table->enum('type', ['preventive', 'corrective', 'curative'])->nullable();
            $table->date('date_maintenance')->nullable();
            $table->unsignedInteger('kilometrage')->nullable();

            $table->unsignedBigInteger('technicien_id')->nullable();
            $table->string('technicien_nom')->nullable();

            $table->decimal('cout', 12, 2)->nullable();
            $table->enum('statut', ['planifiée', 'en_cours', 'terminée', 'annulée'])->default('planifiée');

            $table->text('description')->nullable();
            $table->text('pieces_utilisees')->nullable();
            $table->string('duree')->nullable();
            $table->date('prochaine_echeance')->nullable();

            $table->enum('resultat', ['reparee', 'non_reparee', 'partielle', 'a_suivre'])->nullable();
            $table->text('resultat_notes')->nullable();
            $table->dateTime('completed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
