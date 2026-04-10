<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('conges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('agent_nom', 200);
            $table->string('type_conge', 100);
            $table->date('date_debut');
            $table->date('date_fin');
            $table->integer('nombre_jours');
            $table->text('motif')->nullable();
            $table->string('statut', 50)->default('En attente');
            $table->date('date_demande');
            $table->foreignId('valideur_id')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('date_validation')->nullable();
            $table->text('commentaire_valideur')->nullable();
            $table->timestamps();
            $table->index(['date_debut', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conges');
    }
};
