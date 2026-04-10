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
        Schema::create('commande_fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('fournisseur_id')->nullable()->constrained('fournisseurs')->onDelete('cascade');
            $table->foreignId('contrat_id')->nullable()->constrained('contrat_fournisseurs')->onDelete('cascade');
            $table->date('date_commande');
            $table->date('date_livraison_prevue')->nullable();
            $table->date('date_livraison_reelle')->nullable();
            $table->decimal('montant_ht', 12, 2)->default(0);
            $table->decimal('tva', 5, 2)->default(20.00);
            $table->decimal('montant_ttc', 12, 2)->default(0);
            $table->string('statut')->default('en_attente'); // en_attente, validee, envoyee, en_cours, livree, annulee
            $table->string('mode_paiement')->nullable();
            $table->text('conditions_paiement')->nullable();
            $table->decimal('frais_livraison', 12, 2)->default(0);
            $table->decimal('remise', 12, 2)->default(0);
            $table->json('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['fournisseur_id', 'statut']);
            $table->index(['date_commande', 'statut']);
            $table->index('reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commande_fournisseurs');
    }
};
