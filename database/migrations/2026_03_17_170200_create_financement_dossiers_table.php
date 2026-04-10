<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('financement_dossiers')) {
            return;
        }

        Schema::create('financement_dossiers', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->nullable()->unique();
            $table->string('intitule');
            $table->string('type_financement')->nullable();
            $table->string('organisme_cible')->nullable();
            $table->decimal('montant_demande', 15, 2)->nullable();
            $table->decimal('montant_obtenu', 15, 2)->nullable();
            $table->string('devise', 10)->default('XOF');
            $table->date('date_depot')->nullable();
            $table->date('date_validation')->nullable();
            $table->string('statut')->default('en_preparation');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financement_dossiers');
    }
};
