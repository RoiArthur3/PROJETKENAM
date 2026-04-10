<?php

namespace App\Listeners;

use App\Events\VehicleAssigned;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdateVehicleAvailability
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(VehicleAssigned $event): void
    {
        try {
            $vehicle = $event->vehicle;
            
            // Mettre à jour le statut du véhicule
            $vehicle->update([
                'status' => 'assigned',
                'availability' => 'unavailable'
            ]);

            Log::info('Vehicle availability updated after assignment', [
                'vehicle_id' => $vehicle->id,
                'operation_id' => $event->operationId,
                'driver_id' => $event->driverId,
                'new_status' => 'assigned'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update vehicle availability', [
                'vehicle_id' => $event->vehicle->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
