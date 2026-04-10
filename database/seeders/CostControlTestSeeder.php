<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehicule;
use App\Models\VehicleMission;
use App\Models\VehiclePointage;
use App\Models\VehicleFinancialEntry;
use App\Models\User;
use Carbon\Carbon;

class CostControlTestSeeder extends Seeder
{
    public function run()
    {
        // Créer un véhicule
        $vehicule = Vehicule::create([
            'type_materiel' => 'Camion',
            'marque' => 'Renault',
            'modele' => 'Premium',
            'immatriculation' => 'TEST-123',
            'annee' => 2020,
            'couleur' => 'Blanc',
            'kilometrage' => 50000,
            'date_achat' => Carbon::now()->subYears(2),
            'disponible' => true,
        ]);

        // Créer un utilisateur (driver)
        $driver = User::firstOrCreate([
            'email' => 'driver@test.com'
        ], [
            'name' => 'Test Driver',
            'role' => 'agent',
            'password' => bcrypt('password')
        ]);

        // Créer une mission
        $mission = VehicleMission::create([
            'vehicle_id' => $vehicule->id,
            'reference' => 'MISSION-TEST',
            'destination' => 'Abidjan',
            'start_at' => Carbon::now()->subDays(5),
            'end_at' => Carbon::now()->subDays(2),
            'driver_id' => $driver->id,
            'duration_days' => 3,
            'daily_supplier_price' => 50000,
            'daily_client_price' => 80000,
            'status' => 'done',
        ]);

        // Créer un pointage
        VehiclePointage::create([
            'vehicle_mission_id' => $mission->id,
            'vehicle_id' => $vehicule->id,
            'driver_id' => $driver->id,
            'date_pointage' => Carbon::now()->subDays(4),
            'unit_type' => 'jour',
            'quantity' => 1,
            'supplier_unit_cost' => 50000,
            'client_unit_price' => 80000,
            'total_supplier_cost' => 50000,
            'total_client_amount' => 80000,
            'notes' => 'Pointage test',
            'created_by' => $driver->id,
        ]);

        // Créer une charge
        VehicleFinancialEntry::create([
            'vehicle_mission_id' => $mission->id,
            'vehicle_id' => $vehicule->id,
            'entry_type' => 'charge',
            'category' => 'Carburant',
            'entry_date' => Carbon::now()->subDays(4),
            'label' => 'Carburant test',
            'amount' => 15000,
            'notes' => 'Charge test',
            'created_by' => $driver->id,
        ]);

        // Créer un CA
        VehicleFinancialEntry::create([
            'vehicle_mission_id' => $mission->id,
            'vehicle_id' => $vehicule->id,
            'entry_type' => 'revenue',
            'category' => 'Prestation',
            'entry_date' => Carbon::now()->subDays(3),
            'label' => 'Prestation test',
            'amount' => 80000,
            'notes' => 'CA test',
            'created_by' => $driver->id,
        ]);
    }
}
