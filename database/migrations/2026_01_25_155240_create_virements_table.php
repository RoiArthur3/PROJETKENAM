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
            $table->foreignId('initie_par')->constrained('users')->onDelete('set null');
            $table->foreignId('valide_par')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('date_validation')->nullable();
            $table->string('reference_operation')->nullable();
            $table->decimal('frais', 15, 2)->default(0);
            $table->string('devise', 3)->default('XOF');
            $table->decimal('taux_change', 15, 6)->default(1);
            $table->timestamps();
            $table->softDeletes();

            // Index pour les recherches fréquentes
            $table->index('reference');
            $table->index('date_virement');
            $table->index('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virements');
    }
};
