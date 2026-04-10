<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('paiement_fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('fournisseur_id')->constrained('fournisseurs')->cascadeOnDelete();
            $table->foreignId('facture_id')->constrained('facture_fournisseurs')->cascadeOnDelete();
            $table->date('date_paiement');
            $table->decimal('montant', 10, 2);
            $table->string('mode_paiement');
            $table->string('reference_paiement')->nullable();
            $table->date('date_encaissement')->nullable();
            $table->boolean('est_encaisse')->default(false);
            $table->boolean('est_annule')->default(false);
            $table->string('motif_annulation')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('validateur_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('paiement_fournisseurs');
    }
};
