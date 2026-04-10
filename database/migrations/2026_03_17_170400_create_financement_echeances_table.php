<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('financement_echeances')) {
            return;
        }

        Schema::create('financement_echeances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offre_id')->constrained('financement_offres_bancaires')->cascadeOnDelete();
            $table->integer('numero_echeance')->default(1);
            $table->date('date_echeance');
            $table->decimal('capital', 15, 2)->default(0);
            $table->decimal('interet', 15, 2)->default(0);
            $table->decimal('mensualite', 15, 2)->default(0);
            $table->decimal('solde_restant', 15, 2)->default(0);
            $table->string('statut')->default('a_payer');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financement_echeances');
    }
};
