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
        if (!Schema::hasTable('historique_commandes')) {
            Schema::create('historique_commandes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('commande_id')->constrained('commandes_recentes');
                $table->enum('action', ['cree', 'modifie', 'supprime', 'statut_change', 'chauffeur_ajoute', 'vehicule_ajoute']);
                $table->text('description')->nullable();
                $table->text('ancienne_valeur')->nullable();
                $table->text('nouvelle_valeur')->nullable();
                $table->foreignId('user_id')->constrained('users');
                $table->timestamp('date_action');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historique_commandes');
    }
};
