<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('commercial_commande_fournisseurs')) {
            return;
        }

        Schema::create('commercial_commande_fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reponse_id');

            $table->string('fournisseur_nom');
            $table->string('engin_disponible')->nullable();
            $table->decimal('prix', 15, 2)->nullable();
            $table->string('devise')->nullable();
            $table->text('details')->nullable();

            $table->timestamps();

            $table->index(['reponse_id']);

            $table->foreign('reponse_id')
                ->references('id')
                ->on('commercial_commande_reponses')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commercial_commande_fournisseurs');
    }
};
