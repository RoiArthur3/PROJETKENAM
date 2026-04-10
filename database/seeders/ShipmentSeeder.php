<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shipment;
use App\Models\Parcel;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ShipmentSeeder extends Seeder
{
    public function run(): void
    {
        $parcels = Parcel::where('status', '!=', 'declared')->get();
        $users = User::where('role', 'client')->get();

        if ($parcels->isEmpty()) {
            return;
        }

        // Créer 50 expéditions
        for ($i = 0; $i < 50; $i++) {
            $count = min($parcels->count(), rand(1, 8));
            $shipmentParcels = $parcels->random($count);
            if (!$shipmentParcels instanceof \Illuminate\Support\Collection) {
                $shipmentParcels = collect([$shipmentParcels]);
            }

            $shipment = Shipment::create([
                'reference' => $this->generateShipmentReference(),
                'transport_mode' => $this->getRandomTransportMode(),
                'origin_country' => $shipmentParcels->first()->origin_country ?? 'China',
                'origin_warehouse' => $shipmentParcels->first()->warehouse_id ? ('Warehouse #' . $shipmentParcels->first()->warehouse_id) : 'Main Warehouse',
                'destination_country' => $shipmentParcels->first()->destination_country ?? $this->getRandomDestination(),
                'destination_warehouse' => null,
                'status' => $this->getRandomShipmentStatus(),
                'departure_date' => Carbon::now()->subDays(rand(0, 30))->toDateString(),
                'estimated_arrival_date' => Carbon::now()->addDays(rand(7, 30))->toDateString(),
                'arrival_date' => rand(0, 1) ? Carbon::now()->subDays(rand(0, 10))->toDateString() : null,
                'carrier_name' => $this->getRandomCarrierName(),
                'tracking_number' => 'TRK' . strtoupper(Str::random(10)),
                'notes' => $this->getRandomShipmentNotes(),
                'created_at' => Carbon::now()->subDays(rand(0, 60)),
            ]);

            // Associer les colis à l'expédition
            foreach ($shipmentParcels as $parcel) {
                $parcel->shipment_id = $shipment->id;
                $parcel->save();
            }
        }
    }

    private function generateShipmentReference(): string
    {
        return 'SHP' . date('Ym') . strtoupper(Str::random(6)) . rand(10, 99);
    }

    private function getRandomShipmentStatus(): string
    {
        $statuses = ['pending', 'in_transit', 'arrived', 'completed'];
        return $statuses[array_rand($statuses)];
    }

    private function getRandomTransportMode(): string
    {
        $modes = ['air_normal', 'air_express', 'sea'];
        return $modes[array_rand($modes)];
    }

    private function getRandomCarrierName(): ?string
    {
        $carriers = [null, 'DHL', 'UPS', 'FedEx', 'China Post', 'Maersk'];
        return $carriers[array_rand($carriers)];
    }

    private function getRandomDestination(): string
    {
        $destinations = [
            'France', 'Belgium', 'Switzerland', 'Germany', 'Italy', 'Spain',
            'Portugal', 'Netherlands', 'Luxembourg', 'Monaco', 'Andorra',
            'United Kingdom', 'Ireland', 'Denmark', 'Sweden', 'Norway',
            'Finland', 'Poland', 'Czech Republic', 'Hungary', 'Romania',
            'Bulgaria', 'Greece', 'Turkey', 'Morocco', 'Algeria', 'Tunisia',
            'Senegal', 'Ivory Coast', 'Cameroon', 'Gabon', 'Congo', 'Madagascar',
            'South Africa', 'Kenya', 'Tanzania', 'Uganda', 'Rwanda', 'Burundi',
            'Mauritius', 'Seychelles', 'Réunion', 'Mayotte', 'New Caledonia',
            'French Polynesia', 'Guadeloupe', 'Martinique', 'Guyane'
        ];
        return $destinations[array_rand($destinations)];
    }

    private function getRandomShipmentNotes(): ?string
    {
        $notes = [
            null,
            'Expedition prioritaire',
            'Contient des produits électroniques',
            'Fragile - Manipulation avec soin',
            'Documents commerciaux inclus',
            'Déclaration en douane préparée',
            'Client demande confirmation de livraison',
            'Suivi par GPS activé',
            'Assurance tous risques',
            'Livraison en point relais',
            'Signature requise à la livraison'
        ];
        return $notes[array_rand($notes)];
    }
}
