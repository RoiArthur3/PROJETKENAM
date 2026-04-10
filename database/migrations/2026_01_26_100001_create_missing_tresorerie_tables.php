<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('banques')) {
            Schema::create('banques', function (Blueprint $table) {
                $table->id();
                $table->string('nom');
                $table->string('code_banque', 50)->nullable();
                $table->string('code_guichet', 50)->nullable();
                $table->string('adresse')->nullable();
                $table->string('ville', 100)->nullable();
                $table->string('pays', 100)->nullable();
                $table->string('telephone', 20)->nullable();
                $table->string('email')->nullable();
                $table->string('site_web')->nullable();
                $table->text('description')->nullable();
                $table->boolean('est_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->unique('code_banque');
                $table->index('nom');
            });
        }

        if (!Schema::hasTable('compte_bancaires')) {
            Schema::create('compte_bancaires', function (Blueprint $table) {
                $table->id();
                $table->foreignId('banque_id')->constrained('banques')->onDelete('cascade');
                $table->string('numero_compte', 50);
                $table->string('intitule_compte');
                $table->string('type_compte')->default('courant');
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

                $table->unique(['banque_id', 'numero_compte']);
                $table->index('numero_compte');
                $table->index('type_compte');
                $table->index('devise');
            });
        }

        if (!Schema::hasTable('virements')) {
            Schema::create('virements', function (Blueprint $table) {
                $table->id();
                $table->string('reference')->unique();
                $table->foreignId('compte_source_id')->constrained('compte_bancaires')->onDelete('restrict');
                $table->foreignId('compte_destination_id')->constrained('compte_bancaires')->onDelete('restrict');
                $table->decimal('montant', 15, 2);
                $table->date('date_virement');
                $table->enum('statut', ['en_attente', 'effectue', 'annule', 'echec'])->default('en_attente');
                $table->string('motif')->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('initie_par')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('valide_par')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('date_validation')->nullable();
                $table->string('reference_operation')->nullable();
                $table->decimal('frais', 15, 2)->default(0);
                $table->string('devise', 3)->default('XOF');
                $table->decimal('taux_change', 15, 6)->default(1);
                $table->timestamps();
                $table->softDeletes();

                $table->index('reference');
                $table->index('date_virement');
                $table->index('statut');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('virements');
        Schema::dropIfExists('compte_bancaires');
        Schema::dropIfExists('banques');
    }
};
