<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('livraison_fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('fournisseur_id')->constrained('fournisseurs')->cascadeOnDelete();
            $table->foreignId('commande_id')->constrained('commande_fournisseurs')->cascadeOnDelete();
            $table->string('bon_livraison')->nullable();
            $table->date('date_livraison');
            $table->date('date_reception')->nullable();
            $table->string('transporteur')->nullable();
            $table->string('numero_suivi')->nullable();
            $table->decimal('frais_transport', 10, 2)->default(0);
            $table->string('statut')->default('en_attente');
            $table->text('notes')->nullable();
            $table->foreignId('receptionnaire_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('ligne_livraison_fournisseur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('livraison_id')->constrained('livraison_fournisseurs')->cascadeOnDelete();
            $table->foreignId('ligne_commande_id')->constrained('ligne_commande_fournisseurs')->cascadeOnDelete();
            $table->decimal('quantite_livree', 10, 3);
            $table->string('lot_serie')->nullable();
            $table->date('date_peremption')->nullable();
            $table->text('commentaire')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ligne_livraison_fournisseur');
        Schema::dropIfExists('livraison_fournisseurs');
    }
};
