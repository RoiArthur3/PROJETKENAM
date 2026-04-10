<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Vérifier si la table existe déjà
        if (Schema::hasTable('vehicle_financial_entries')) {
            return;
        }

        Schema::create('vehicle_financial_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_mission_id')->nullable();
            $table->foreignId('operation_id')->nullable()->constrained('operations')->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicules')->nullOnDelete();
            $table->enum('entry_type', ['charge', 'revenue']);
            $table->enum('category', ['fuel', 'driver_salary', 'maintenance', 'transport', 'site_misc', 'invoice', 'other']);
            $table->date('entry_date');
            $table->string('label');
            $table->decimal('amount', 15, 2);
            $table->enum('source_module', ['manual', 'tresorerie_decaissement', 'tresorerie_avance', 'facture'])->default('manual');
            $table->foreignId('depense_caisse_id')->nullable()->constrained('depense_caisses')->nullOnDelete();
            $table->foreignId('facture_id')->nullable()->constrained('factures')->nullOnDelete();
            $table->string('external_reference')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Ajouter les contraintes de foreign key seulement si les tables existent
            if (Schema::hasTable('vehicle_missions')) {
                $table->foreign('vehicle_mission_id')->references('id')->on('vehicle_missions')->nullOnDelete();
            }

            $table->index(['vehicle_mission_id', 'entry_type', 'entry_date'], 'vehicle_financial_entries_mission_type_date_idx');
            $table->index(['operation_id', 'entry_type', 'entry_date'], 'vehicle_financial_entries_operation_type_date_idx');
            $table->index(['vehicle_id', 'entry_date'], 'vehicle_financial_entries_vehicle_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_financial_entries');
    }
};
