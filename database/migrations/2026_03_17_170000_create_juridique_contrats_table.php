<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('juridique_contrats')) {
            return;
        }

        Schema::create('juridique_contrats', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->nullable()->unique();
            $table->string('titre');
            $table->string('type_contrat')->nullable();
            $table->string('partie_contractante')->nullable();
            $table->text('objet')->nullable();
            $table->date('date_signature')->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->decimal('montant', 15, 2)->nullable();
            $table->string('devise', 10)->default('XOF');
            $table->string('statut')->default('brouillon');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('juridique_contrats');
    }
};
