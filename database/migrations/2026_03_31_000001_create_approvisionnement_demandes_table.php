<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approvisionnement_demandes', function (Blueprint $table) {
            $table->id();
            $table->string('numero_demande')->unique();
            $table->decimal('montant', 15, 2);
            $table->string('devise', 10)->default('XOF');
            $table->text('raison')->nullable();
            $table->string('statut', 20)->default('pending');
            // pending | approved | rejected | executed
            $table->unsignedBigInteger('caisse_source_id')->nullable();
            $table->unsignedBigInteger('caisse_destination_id')->nullable();
            $table->unsignedBigInteger('demandeur_id');
            $table->unsignedBigInteger('approuve_par_id')->nullable();
            $table->unsignedBigInteger('approvisionnement_caisse_id')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index(['statut', 'created_at']);
            $table->index('demandeur_id');
            $table->index('approuve_par_id');

            $table->foreign('caisse_source_id')->references('id')->on('caisses')->nullOnDelete();
            $table->foreign('caisse_destination_id')->references('id')->on('caisses')->nullOnDelete();
            $table->foreign('demandeur_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('approuve_par_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approvisionnement_demandes');
    }
};
