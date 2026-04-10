<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('recettes')) {
            return;
        }

        Schema::create('recettes', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('libelle');
            $table->decimal('montant', 15, 2);
            $table->date('date_recette');
            $table->string('categorie')->nullable();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('caisse_id')->nullable()->constrained('caisses')->nullOnDelete();
            $table->string('mode_paiement')->nullable();
            $table->string('statut')->default('en_attente');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recettes');
    }
};
