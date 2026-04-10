<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('validations')) {
            Schema::create('validations', function (Blueprint $table) {
                $table->id();
                $table->string('module_source'); // 'operations', 'parc_auto', 'rh', 'comptabilite'
                $table->unsignedBigInteger('record_id'); // ID de l'enregistrement source
                $table->string('type'); // 'operation', 'demande_achat', 'conge', 'facture'
                $table->string('titre');
                $table->text('description')->nullable();
                $table->unsignedBigInteger('initiateur_id')->nullable();
                $table->unsignedBigInteger('validateur_id')->nullable();
                $table->enum('statut', ['en_attente', 'valide', 'rejete', 'corrige', 'en_cours'])->default('en_attente');
                $table->text('commentaire')->nullable();
                $table->timestamp('date_validation')->nullable();
                $table->json('workflow_data')->nullable(); // Données du workflow (étapes, etc.)
                $table->timestamps();

                $table->index(['module_source', 'record_id']);
                $table->index(['statut']);
                $table->index(['validateur_id']);
                $table->index(['initiateur_id']);
            });
        }

        if (!Schema::hasTable('validation_logs')) {
            Schema::create('validation_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('validation_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('action'); // 'created', 'validated', 'rejected', 'corrected'
                $table->text('commentaire')->nullable();
                $table->json('ancien_statut')->nullable();
                $table->json('nouveau_statut')->nullable();
                $table->timestamps();

                $table->foreign('validation_id')->references('id')->on('validations')->onDelete('cascade');
                $table->index(['validation_id', 'action']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('validation_logs');
        Schema::dropIfExists('validations');
    }
};
