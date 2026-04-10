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
        if (!Schema::hasTable('commande_fournisseurs')) {
            Schema::create('commande_fournisseurs', function (Blueprint $table) {
                $table->id();
                $table->string('reference')->unique();
                $table->foreignId('fournisseur_id')->constrained('fournisseurs')->onDelete('cascade');
                $table->date('date_commande');
                $table->decimal('montant_ht', 15, 2);
                $table->decimal('tva', 15, 2)->default(0);
                $table->decimal('montant_ttc', 15, 2);
                $table->enum('statut', ['en_attente', 'confirmee', 'en_cours', 'livree', 'annulee'])->default('en_attente');
                $table->date('date_livraison_prevue')->nullable();
                $table->date('date_livraison_reelle')->nullable();
                $table->string('service_demandeur')->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index('reference');
                $table->index('fournisseur_id');
                $table->index('statut');
                $table->index('date_commande');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commande_fournisseurs');
    }
};
