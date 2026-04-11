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
            // Ajouter les colonnes seulement si elles n'existent pas
            if (!Schema::hasColumn('pointages', 'vehicle_id')) {
                $table->unsignedBigInteger('vehicle_id')->nullable()->after('user_id')->comment('ID du véhicule');
            }
            if (!Schema::hasColumn('pointages', 'operation_id')) {
                $table->unsignedBigInteger('operation_id')->nullable()->after('vehicle_id')->comment('ID de l\'opération/projet');
            }
            if (!Schema::hasColumn('pointages', 'unit_type')) {
                $table->string('unit_type')->nullable()->after('date_pointage')->comment('Type d\'unité (heure, jour, voyage)');
            }
            if (!Schema::hasColumn('pointages', 'quantity')) {
                $table->decimal('quantity', 8, 2)->nullable()->after('unit_type')->comment('Quantité');
            }
            if (!Schema::hasColumn('pointages', 'supplier_unit_cost')) {
                $table->decimal('supplier_unit_cost', 10, 2)->nullable()->after('quantity')->comment('Coût unitaire fournisseur');
            }
            if (!Schema::hasColumn('pointages', 'client_unit_price')) {
                $table->decimal('client_unit_price', 10, 2)->nullable()->after('supplier_unit_cost')->comment('Prix unitaire client');
            }
            if (!Schema::hasColumn('pointages', 'total_supplier_cost')) {
                $table->decimal('total_supplier_cost', 12, 2)->nullable()->after('client_unit_price')->comment('Coût total fournisseur');
            }
            if (!Schema::hasColumn('pointages', 'total_client_amount')) {
                $table->decimal('total_client_amount', 12, 2)->nullable()->after('total_supplier_cost')->comment('Montant total client');
            }
            if (!Schema::hasColumn('pointages', 'submodule')) {
                $table->string('submodule')->nullable()->after('total_client_amount')->comment('Sous-module (engin, camion_plateau)');
            }
            if (!Schema::hasColumn('pointages', 'billing_mode')) {
                $table->string('billing_mode')->nullable()->after('submodule')->comment('Mode de facturation');
            }
            if (!Schema::hasColumn('pointages', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('user_id')->comment('ID du créateur');
            }

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

            // Index seulement s'ils n'existent pas déjà
            if (!Schema::hasIndex('pointages', 'pointages_vehicle_id_index')) {
                $table->index('vehicle_id');
            }
            if (!Schema::hasIndex('pointages', 'pointages_operation_id_index')) {
                $table->index('operation_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pointages', function (Blueprint $table) {
            // Supprimer les index seulement s'ils existent
            if (Schema::hasIndex('pointages', 'pointages_vehicle_id_index')) {
                $table->dropIndex(['vehicle_id']);
            }
            if (Schema::hasIndex('pointages', 'pointages_operation_id_index')) {
                $table->dropIndex(['operation_id']);
            }

            // Supprimer les colonnes seulement si elles existent
            $columnsToDrop = [
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
            ];

            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('pointages', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
