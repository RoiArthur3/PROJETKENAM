<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Parcel;
use App\Models\ParcelPhoto;
use App\Models\ParcelStatusHistory;
use App\Models\Shipment;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ParcelSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'client')->get();
        $warehouses = Warehouse::all();

        $transportModes = ['air_express', 'air_normal', 'sea'];
        $statuses = ['announced', 'received', 'inspected', 'rejected', 'grouped', 'in_transit', 'arrived', 'fees_calculated', 'paid', 'delivered'];

        // Créer 100 colis avec des données réalistes
        for ($i = 0; $i < 100; $i++) {
            $user = $users->random();
            $status = $statuses[array_rand($statuses)];
            $transportMode = $transportModes[array_rand($transportModes)];
            $warehouse = $warehouses->random();

            $parcel = Parcel::create([
                'user_id' => $user->id,
                'shipment_id' => null, // Sera assigné plus tard
                'warehouse_id' => $warehouse->id,
                'tracking_number' => $this->generateTrackingNumber(),
                'transport_mode' => $transportMode,
                'status' => $status,
                'content_type' => $this->getRandomContentType(),
                'origin_country' => $this->getRandomOriginCountry(),
                'origin_city' => $this->getRandomOriginCity(),
                'destination_country' => $this->getRandomDestinationCountry(),
                'destination_city' => $this->getRandomDestinationCity(),
                'delivery_option' => $this->getRandomDeliveryOption(),
                'content_description' => $this->getRandomContentDescription(),
                'declared_value' => rand(1000, 50000) / 100, // 10€ à 500€
                'estimated_weight' => rand(500, 5000) / 100, // 0.5kg à 50kg
                'actual_weight' => in_array($status, ['received', 'inspected', 'grouped', 'in_transit', 'arrived', 'fees_calculated', 'paid', 'delivered']) ? rand(500, 5000) / 100 : null,
                'length' => in_array($status, ['received', 'inspected', 'grouped', 'in_transit', 'arrived', 'fees_calculated', 'paid', 'delivered']) ? rand(10, 60) : null,
                'width' => in_array($status, ['received', 'inspected', 'grouped', 'in_transit', 'arrived', 'fees_calculated', 'paid', 'delivered']) ? rand(10, 60) : null,
                'height' => in_array($status, ['received', 'inspected', 'grouped', 'in_transit', 'arrived', 'fees_calculated', 'paid', 'delivered']) ? rand(10, 60) : null,
                'volumetric_weight' => null, // Sera calculé
                'chargeable_weight' => null, // Sera calculé
                'received_at_warehouse_date' => in_array($status, ['received', 'inspected', 'grouped', 'in_transit', 'arrived', 'fees_calculated', 'paid', 'delivered'])
                    ? Carbon::now()->subDays(rand(1, 30)) : null,
                'inspection_date' => in_array($status, ['inspected', 'grouped', 'in_transit', 'arrived', 'fees_calculated', 'paid', 'delivered'])
                    ? Carbon::now()->subDays(rand(1, 25)) : null,
                'delivered_date' => $status === 'delivered' ? Carbon::now()->subDays(rand(1, 15)) : null,
                'rejection_reason' => $status === 'rejected' ? $this->getRandomRejectionReason() : null,
                'notes' => $this->getRandomNotes(),
                'created_at' => Carbon::now()->subDays(rand(0, 90)),
            ]);

            // Calculer les poids
            if ($parcel->actual_weight && $parcel->length && $parcel->width && $parcel->height) {
                $parcel->volumetric_weight = ($parcel->length * $parcel->width * $parcel->height) / 5000;
                $parcel->chargeable_weight = max($parcel->actual_weight, $parcel->volumetric_weight);
                $parcel->save();
            }
        }
    }

    private function generateTrackingNumber(): string
    {
        return 'GRP' . date('Y') . strtoupper(Str::random(8)) . rand(100, 999);
    }

    private function getRandomContentType(): string
    {
        $types = ['electronics', 'clothing', 'books', 'cosmetics', 'food', 'toys', 'sports', 'home', 'health', 'other'];
        return $types[array_rand($types)];
    }

    private function getRandomOriginCountry(): string
    {
        $countries = ['United States', 'China', 'United Kingdom', 'Germany', 'France', 'Canada', 'Japan', 'South Korea', 'Australia', 'Netherlands'];
        return $countries[array_rand($countries)];
    }

    private function getRandomOriginCity(): string
    {
        $cities = ['New York', 'Los Angeles', 'Shanghai', 'Guangzhou', 'London', 'Manchester', 'Berlin', 'Munich', 'Paris', 'Lyon', 'Toronto', 'Vancouver', 'Tokyo', 'Osaka', 'Sydney', 'Melbourne', 'Amsterdam', 'Rotterdam'];
        return $cities[array_rand($cities)];
    }

    private function getRandomDestinationCountry(): string
    {
        $countries = ['France', 'Belgium', 'Switzerland', 'Germany', 'Italy', 'Spain', 'Portugal', 'Netherlands', 'Luxembourg', 'Monaco'];
        return $countries[array_rand($countries)];
    }

    private function getRandomDestinationCity(): string
    {
        $cities = ['Paris', 'Lyon', 'Marseille', 'Lille', 'Bordeaux', 'Toulouse', 'Nice', 'Nantes', 'Strasbourg', 'Montpellier', 'Brussels', 'Antwerp', 'Geneva', 'Zurich', 'Berlin', 'Munich', 'Rome', 'Milan', 'Barcelona', 'Madrid'];
        return $cities[array_rand($cities)];
    }

    private function getRandomDeliveryOption(): string
    {
        $options = ['standard', 'express', 'pickup', 'relay'];
        return $options[array_rand($options)];
    }

    private function getRandomContentDescription(): string
    {
        $descriptions = [
            'Electronics and accessories',
            'Clothing and fashion items',
            'Books and educational materials',
            'Cosmetics and beauty products',
            'Food items and supplements',
            'Toys and games',
            'Sports equipment',
            'Home decoration items',
            'Health and medical supplies',
            'Various personal items'
        ];
        return $descriptions[array_rand($descriptions)];
    }

    private function getRandomRejectionReason(): string
    {
        $reasons = [
            'Prohibited items detected',
            'Incorrect declaration',
            'Damaged packaging',
            'Missing documentation',
            'Exceeds weight limits',
            'Hazardous materials',
            'Customs violation',
            'Incomplete information'
        ];
        return $reasons[array_rand($reasons)];
    }

    private function getRandomNotes(): ?string
    {
        $notes = [
            null,
            'Fragile - Handle with care',
            'Electronics - Original packaging',
            'Clothing items',
            'Books and documents',
            'Cosmetics - Check regulations',
            'Gift wrapped',
            'Urgent delivery requested',
            'Consolidate with other parcels',
            'Customs declaration needed'
        ];
        return $notes[array_rand($notes)];
    }
}
