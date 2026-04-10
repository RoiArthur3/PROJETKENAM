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
        if (!Schema::hasTable('factures')) {
            Schema::create('factures', function (Blueprint $table) {
                $table->id();
                $table->string('numero_facture')->unique();
                $table->foreignId('client_id')->nullable()->constrained()->onDelete('set null');
                $table->foreignId('operation_id')->nullable()->constrained()->onDelete('set null');
                $table->date('date_facture');
                $table->date('date_echeance');
                $table->decimal('montant_ht', 12, 2);
                $table->decimal('montant_tva', 12, 2);
                $table->decimal('montant_ttc', 12, 2);
                $table->enum('statut', ['payée', 'impayée', 'partiellement_payée', 'annulée'])->default('impayée');
                $table->string('mode_paiement')->nullable();
                $table->date('date_paiement')->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();

                $table->index(['statut', 'date_facture']);
                $table->index(['date_echeance', 'statut']);
            });
        } else {
            // Table already exists, you can add any necessary column updates here if needed
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
