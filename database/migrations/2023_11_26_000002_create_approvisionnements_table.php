<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('approvisionnements', function (Blueprint $table) {
            $table->id();
            $table->string('numero_operation')->unique();
            $table->foreignId('caisse_source_id')->constrained('caisses');
            $table->foreignId('caisse_destination_id')->constrained('caisses');
            $table->decimal('montant', 15, 2);
            $table->enum('statut', ['en_attente', 'valide', 'decaisse', 'justifie', 'cloture', 'rejete'])->default('en_attente');
            $table->text('motif');
            $table->foreignId('demandeur_id')->constrained('users');
            $table->foreignId('validateur_id')->nullable()->constrained('users');
            $table->timestamp('date_validation')->nullable();
            $table->timestamp('date_decaissement')->nullable();
            $table->timestamp('date_cloture')->nullable();
            $table->text('commentaire_rejet')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('approvisionnements');
    }
};
