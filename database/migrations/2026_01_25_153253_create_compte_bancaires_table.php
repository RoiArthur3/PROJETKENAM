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
        if (Schema::hasTable('compte_bancaires')) {
            return;
        }

        Schema::create('compte_bancaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('banque_id')->constrained()->onDelete('cascade');
            $table->string('numero_compte', 50);
            $table->string('intitule_compte');
            $table->string('type_compte')->default('courant'); // courant, epargne, etc.
            $table->string('devise', 3)->default('XOF');
            $table->decimal('solde', 20, 2)->default(0);
            $table->decimal('solde_ouverture', 20, 2)->default(0);
            $table->date('date_ouverture');
            $table->date('date_fermeture')->nullable();
            $table->string('nom_titulaire');
            $table->string('adresse_titulaire')->nullable();
            $table->string('telephone_titulaire', 20)->nullable();
            $table->string('email_titulaire')->nullable();
            $table->string('nom_contact')->nullable();
            $table->string('telephone_contact', 20)->nullable();
            $table->string('email_contact')->nullable();
            $table->decimal('decouvert_autorise', 20, 2)->default(0);
            $table->decimal('taux_interet', 5, 2)->default(0);
            $table->text('informations_supplementaires')->nullable();
            $table->boolean('est_actif')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->unique(['banque_id', 'numero_compte']);
            $table->index('numero_compte');
            $table->index('type_compte');
            $table->index('devise');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compte_bancaires');
    }
};
