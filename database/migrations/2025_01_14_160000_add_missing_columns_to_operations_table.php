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
        Schema::table('operations', function (Blueprint $table) {
            // Ajouter les colonnes manquantes si elles n'existent pas déjà
            if (!Schema::hasColumn('operations', 'titre')) {
                $table->string('titre')->nullable();
            }
            if (!Schema::hasColumn('operations', 'priorite')) {
                $table->string('priorite')->default('moyenne');
            }
            if (!Schema::hasColumn('operations', 'echeance')) {
                $table->date('echeance')->nullable();
            }
            if (!Schema::hasColumn('operations', 'statut_courant')) {
                $table->string('statut_courant')->default('draft');
            }
            if (!Schema::hasColumn('operations', 'demandeur_name')) {
                $table->string('demandeur_name')->nullable();
            }
            if (!Schema::hasColumn('operations', 'demandeur_email')) {
                $table->string('demandeur_email')->nullable();
            }
            if (!Schema::hasColumn('operations', 'type_operation_id')) {
                $table->foreignId('type_operation_id')->nullable();
            }
            if (!Schema::hasColumn('operations', 'operational_service_id')) {
                $table->foreignId('operational_service_id')->nullable();
            }
            if (!Schema::hasColumn('operations', 'service')) {
                $table->string('service')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operations', function (Blueprint $table) {
            $table->dropColumn([
                'titre',
                'priorite',
                'echeance',
                'statut_courant',
                'demandeur_name',
                'demandeur_email',
                'type_operation_id',
                'operational_service_id',
                'service'
            ]);
        });
    }
};
