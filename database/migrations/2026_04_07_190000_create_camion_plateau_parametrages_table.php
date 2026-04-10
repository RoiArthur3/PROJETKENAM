<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('camion_plateau_parametrages')) {
            return;
        }

        Schema::create('camion_plateau_parametrages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();

            // Mode de facturation client
            $table->enum('type_facturation', ['monthly', 'trip'])->default('trip')->comment('monthly=forfait mois, trip=à la tâche');
            $table->integer('monthly_trip_threshold')->default(0)->comment('Nb voyages inclus dans le forfait mensuel');
            $table->decimal('monthly_flat_rate', 15, 2)->default(0)->comment('Forfait mensuel (FCFA)');
            $table->decimal('extra_trip_unit_price', 15, 2)->default(0)->comment('Prix unitaire voyage supplémentaire (FCFA)');
            $table->decimal('trip_client_price', 15, 2)->default(0)->comment('Prix client par voyage (FCFA)');

            // Mode de paiement fournisseur
            $table->enum('supplier_type_paiement', ['monthly', 'trip'])->default('trip');
            $table->decimal('supplier_monthly_cost', 15, 2)->default(0)->comment('Coût fournisseur mensuel (FCFA)');
            $table->decimal('supplier_trip_cost', 15, 2)->default(0)->comment('Coût fournisseur par voyage (FCFA)');

            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('vehicle_id')->references('id')->on('vehicules')->nullOnDelete();
            $table->foreign('client_id')->references('id')->on('clients')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('camion_plateau_parametrages');
    }
};
