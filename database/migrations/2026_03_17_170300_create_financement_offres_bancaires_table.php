<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('financement_offres_bancaires')) {
            return;
        }

        Schema::create('financement_offres_bancaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_id')->constrained('financement_dossiers')->cascadeOnDelete();
            $table->string('reference')->nullable()->unique();
            $table->string('banque');
            $table->decimal('montant_propose', 15, 2)->nullable();
            $table->decimal('taux_interet', 8, 2)->nullable();
            $table->integer('duree_mois')->nullable();
            $table->date('date_proposition')->nullable();
            $table->date('date_acceptation')->nullable();
            $table->string('statut')->default('proposee');
            $table->text('conditions')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financement_offres_bancaires');
    }
};
