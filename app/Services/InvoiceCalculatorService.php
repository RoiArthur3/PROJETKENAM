<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Parcel;
use App\Models\Tariff;

class InvoiceCalculatorService
{
    public static function calculateInvoiceCosts(Parcel $parcel): array
    {
        $weight = $parcel->weight ?? $parcel->declared_weight ?? 0;
        $contentType = $parcel->content_type;

        // Calculer le volume pour les envois maritimes
        $volume = 0;
        if ($parcel->length && $parcel->width && $parcel->height) {
            $volume = ($parcel->length * $parcel->width * $parcel->height) / 1000000; // Convertir en m³
        }

        // Chercher le tarif applicable
        $tariff = null;

        // Priorité à la tarification par volume pour le maritime
        if ($parcel->transport_mode === 'sea' && $volume > 0) {
            $tariff = Tariff::findApplicableTariffByVolume(
                $parcel->transport_mode,
                $parcel->origin_country,
                $parcel->destination_country,
                $volume,
                $contentType
            );
        }

        // Si pas de tarif par volume, chercher par poids
        if (!$tariff) {
            $tariff = Tariff::findApplicableTariff(
                $parcel->transport_mode,
                $parcel->origin_country,
                $parcel->destination_country,
                $weight,
                $contentType
            );
        }

        if (!$tariff) {
            return [
                'total_amount' => 0,
                'shipping_cost' => 0,
                'insurance_fee' => 0,
                'customs_fee' => 0,
                'handling_fee' => 0,
                'cartons_cost' => 0,
                'breakdown' => []
            ];
        }

        // Calcul selon le type de tarification
        $shippingCost = 0;
        $breakdownDetails = [];

        switch ($tariff->pricing_type) {
            case 'per_piece':
                $shippingCost = $tariff->fixed_fee;
                $breakdownDetails[] = 'Prix par pièce: ' . number_format($tariff->fixed_fee, 2) . ' XOF';
                break;

            case 'per_cbm':
                $shippingCost = $volume * $tariff->price_per_kg;
                $breakdownDetails[] = 'Volume: ' . number_format($volume, 3) . ' m³';
                $breakdownDetails[] = 'Prix par m³: ' . number_format($tariff->price_per_kg, 2) . ' XOF';
                $breakdownDetails[] = 'Coût maritime: ' . number_format($shippingCost, 2) . ' XOF';
                break;

            case 'per_kg':
            default:
                $shippingCost = $weight * $tariff->price_per_kg + $tariff->fixed_fee;
                $breakdownDetails[] = 'Poids facturé: ' . $weight . ' kg';
                $breakdownDetails[] = 'Prix par kg: ' . number_format($tariff->price_per_kg, 2) . ' XOF';
                if ($tariff->fixed_fee > 0) {
                    $breakdownDetails[] = 'Frais fixe: ' . number_format($tariff->fixed_fee, 2) . ' XOF';
                }
                $breakdownDetails[] = 'Coût transport: ' . number_format($shippingCost, 2) . ' XOF';
                break;
        }

        // Frais de cartons (si applicable)
        $cartonsCost = $parcel->cartons_cost ?? 0;

        $totalAmount = $shippingCost + $cartonsCost;

        return [
            'total_amount' => $totalAmount,
            'shipping_cost' => $shippingCost,
            'insurance_fee' => 0,
            'customs_fee' => 0,
            'handling_fee' => 0,
            'cartons_cost' => $cartonsCost,
            'breakdown' => array_merge($breakdownDetails, [
                'Frais cartons: ' . number_format($cartonsCost, 2) . ' XOF',
                'Total: ' . number_format($totalAmount, 2) . ' XOF'
            ])
        ];
    }

    public static function updateInvoiceFromParcel(Invoice $invoice, Parcel $parcel): void
    {
        $costs = self::calculateInvoiceCosts($parcel);

        $invoice->update([
            'total_amount' => $costs['total_amount'],
            'shipping_cost' => $costs['shipping_cost'],
            'insurance_fee' => $costs['insurance_fee'],
            'customs_fee' => $costs['customs_fee'],
            'handling_fee' => $costs['handling_fee'],
            'cartons_cost' => $costs['cartons_cost'],
            'subtotal' => $costs['total_amount'],
        ]);
    }
}
