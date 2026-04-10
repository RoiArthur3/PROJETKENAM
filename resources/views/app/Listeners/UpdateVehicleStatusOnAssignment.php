<?php

namespace App\Listeners;

use App\Events\VehicleAssigned;
use App\Models\Vehicule;
use Illuminate\Support\Facades\Log;

class UpdateVehicleStatusOnAssignment
{
    /**
     * Handle the event.
     */
    public function handle(VehicleAssigned $event): void
    {
        try {
            // Mettre à jour le statut du véhicule quand il est assigné
            $vehicle = Vehicule::find($event->vehicleAssignment->vehicle_id);
            
            if ($vehicle) {
                $vehicle->update([
                    'statut' => 'en_mission',
                    'date_derniere_affectation' => now()
                ]);

                Log::info('Véhicule mis à jour pour mission', [
                    'vehicle_id' => $vehicle->id,
                    'assignment_id' => $event->vehicleAssignment->id,
                    'operation_id' => $event->vehicleAssignment->operation_id
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du statut du véhicule', [
                'assignment_id' => $event->vehicleAssignment->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
