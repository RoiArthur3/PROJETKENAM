<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('facture_fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('fournisseur_id')->constrained('fournisseurs')->cascadeOnDelete();
            $table->foreignId('commande_id')->constrained('commande_fournisseurs')->cascadeOnDelete();
            $table->string('numero_facture')->unique();
            $table->date('date_facture');
            $table->date('date_echeance');
            $table->string('conditions_paiement');
            $table->decimal('frais_livraison', 10, 2)->default(0);
            $table->decimal('remise', 10, 2)->default(0);
            $table->string('type_remise')->default('pourcentage');
            $table->decimal('montant_ht', 10, 2);
            $table->decimal('tva', 10, 2);
            $table->decimal('montant_ttc', 10, 2);
            $table->decimal('montant_paye', 10, 2)->default(0);
            $table->decimal('reste_a_payer', 10, 2);
            $table->string('statut')->default('non_payee');
            $table->date('date_paiement')->nullable();
            $table->text('notes')->nullable();
            $table->string('motif_annulation')->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('ligne_facture_fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facture_id')->constrained('facture_fournisseurs')->cascadeOnDelete();
            $table->foreignId('ligne_commande_id')->constrained('ligne_commande_fournisseurs')->cascadeOnDelete();
            $table->decimal('quantite', 10, 3);
            $table->decimal('prix_unitaire_ht', 10, 2);
            $table->decimal('tva_taux', 5, 2);
            $table->decimal('remise', 5, 2)->default(0);
            $table->decimal('montant_ht', 10, 2);
            $table->decimal('montant_tva', 10, 2);
            $table->decimal('montant_ttc', 10, 2);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ligne_facture_fournisseurs');
        Schema::dropIfExists('facture_fournisseurs');
    }
};
