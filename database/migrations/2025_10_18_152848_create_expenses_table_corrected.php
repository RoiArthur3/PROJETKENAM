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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('categorie');
            $table->text('description')->nullable();
            $table->decimal('montant', 10, 2);
            $table->date('date_depense');
            $table->string('statut')->default('en_attente'); // en_attente, approuvee, rejetee
            
            // Clés étrangères
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('operation_id')->nullable();
            // La contrainte vers operations sera ajoutée plus tard
            
            // Informations supplémentaires
            $table->string('fournisseur')->nullable();
            $table->string('justificatif')->nullable();
            $table->enum('mode_paiement', ['espece', 'cheque', 'virement', 'carte_bancaire']);
            $table->string('service_concerne');
            $table->decimal('budget_prevu', 10, 2)->nullable();
            
            // Approbation
            $table->foreignId('approuve_par')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('date_approbation')->nullable();
            $table->text('rejet_raison')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['approuve_par']);
            // La contrainte vers operations sera supprimée plus tard
        });
        
        Schema::dropIfExists('expenses');
    }
};
