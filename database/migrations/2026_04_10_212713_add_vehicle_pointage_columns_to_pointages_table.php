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
        Schema::table('pointages', function (Blueprint $table) {
            $table->unsignedBigInteger('vehicle_id')->nullable()->after('user_id')->comment('ID du véhicule');
            $table->unsignedBigInteger('operation_id')->nullable()->after('vehicle_id')->comment('ID de l\'opération/projet');
            $table->string('unit_type')->nullable()->after('date_pointage')->comment('Type d\'unité (heure, jour, voyage)');
            $table->decimal('quantity', 8, 2)->nullable()->after('unit_type')->comment('Quantité');
            $table->decimal('supplier_unit_cost', 10, 2)->nullable()->after('quantity')->comment('Coût unitaire fournisseur');
            $table->decimal('client_unit_price', 10, 2)->nullable()->after('supplier_unit_cost')->comment('Prix unitaire client');
            $table->decimal('total_supplier_cost', 12, 2)->nullable()->after('client_unit_price')->comment('Coût total fournisseur');
            $table->decimal('total_client_amount', 12, 2)->nullable()->after('total_supplier_cost')->comment('Montant total client');
            $table->string('submodule')->nullable()->after('total_client_amount')->comment('Sous-module (engin, camion_plateau)');
            $table->string('billing_mode')->nullable()->after('submodule')->comment('Mode de facturation');
            $table->unsignedBigInteger('created_by')->nullable()->after('user_id')->comment('ID du créateur');

            // Rendre les colonnes existantes nullable si nécessaire
            if (Schema::hasColumn('pointages', 'heure_arrivee')) {
                $table->dateTime('heure_arrivee')->nullable()->change();
            }
            if (Schema::hasColumn('pointages', 'heure_depart')) {
                $table->dateTime('heure_depart')->nullable()->change();
            }
            if (Schema::hasColumn('pointages', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->change();
            }

            // Index
            $table->index('vehicle_id');
            $table->index('operation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pointages', function (Blueprint $table) {
            $table->dropIndex(['vehicle_id']);
            $table->dropIndex(['operation_id']);
            $table->dropColumn([
                'vehicle_id',
                'operation_id',
                'unit_type',
                'quantity',
                'supplier_unit_cost',
                'client_unit_price',
                'total_supplier_cost',
                'total_client_amount',
                'submodule',
                'billing_mode',
                'created_by'
            ]);
        });
    }
};
