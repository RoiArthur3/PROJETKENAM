<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Parcel;
use App\Models\Invoice;
use App\Services\InvoiceCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ShipmentWebController extends Controller
{
    public function create()
    {
        return view('shipments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transport_mode' => 'required|in:air_normal,air_express,sea',
            'content_type' => 'required|string',
            'content_description' => 'required|string',
            'declared_value' => 'nullable|numeric|min:0',
            'estimated_weight' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'origin_country' => 'required|string',
            'origin_city' => 'required|string',
            'destination_country' => 'required|string',
            'destination_city' => 'required|string',
            'delivery_option' => 'required|in:pickup,delivery',
            'invoice_file' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:5120',
            'create_invoice' => 'nullable|boolean',
            'cartons_cost' => 'nullable|numeric|min:0'
        ]);

        // Création du colis
        $parcel = Parcel::create([
            'user_id' => Auth::id(),
            'transport_mode' => $validated['transport_mode'],
            'content_type' => $validated['content_type'],
            'content_description' => $validated['content_description'],
            'declared_value' => $validated['declared_value'] ?? null,
            'estimated_weight' => $validated['estimated_weight'] ?? null,
            'length' => $validated['length'] ?? null,
            'width' => $validated['width'] ?? null,
            'height' => $validated['height'] ?? null,
            'origin_country' => $validated['origin_country'],
            'origin_city' => $validated['origin_city'],
            'destination_country' => $validated['destination_country'],
            'destination_city' => $validated['destination_city'],
            'delivery_option' => $validated['delivery_option'],
            'status' => 'declared'
        ]);

        // Génération du numéro de suivi unique
        $trackingNumber = 'GRP-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        $parcel->tracking_number = $trackingNumber;
        $parcel->save();

        // Gestion des fichiers
        if ($request->hasFile('invoice_file')) {
            $path = $request->file('invoice_file')->store('invoices', 'public');
            $parcel->invoice_file = $path;
            $parcel->save();
        }

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('parcels/' . $parcel->id, 'public');
                $parcel->photos()->create([
                    'file_path' => $path,
                    'file_name' => $photo->getClientOriginalName(),
                    'file_size' => $photo->getSize(),
                    'mime_type' => $photo->getMimeType(),
                    'type' => 'package'
                ]);
            }
        }

        // Création automatique de la facture si demandé
        $invoice = null;
        if (isset($validated['create_invoice']) && $validated['create_invoice']) {
            $invoice = $this->createInvoiceFromShipment($parcel, $validated);
        }

        // Redirection avec message approprié
        if ($invoice) {
            return redirect()->route('invoices.show', $invoice->id)
                ->with('success', 'Votre colis a été déclaré avec succès et la facture a été générée. Numéro de suivi : ' . $trackingNumber);
        }

        return redirect()->route('parcels.show', $parcel->id)
            ->with('success', 'Votre colis a été déclaré avec succès. Numéro de suivi : ' . $trackingNumber);
    }

    private function createInvoiceFromShipment(Parcel $parcel, array $shipmentData): Invoice
    {
        // Calcul des coûts basés sur la simulation
        $weight = $shipmentData['estimated_weight'] ?? 1;
        $declaredValue = $shipmentData['declared_value'] ?? 0; // Déjà en FCFA
        $transportMode = $shipmentData['transport_mode'];

        // Calcul du coût de transport selon le mode (tarifs directement en FCFA)
        $shippingCost = 0;
        switch ($transportMode) {
            case 'air_normal':
                $shippingCost = $weight * 5250; // 8€ * 655.957 = 5250 FCFA/kg
                break;
            case 'air_express':
                $shippingCost = $weight * 7875; // 12€ * 655.957 = 7875 FCFA/kg
                break;
            case 'sea':
                $shippingCost = $weight * 2625; // 4€ * 655.957 = 2625 FCFA/kg
                break;
        }

        // Calcul des autres frais (tous en FCFA)
        $customsFee = $declaredValue > 0 ? $declaredValue * 0.15 : 0; // 15% de douane sur la valeur en FCFA
        $handlingFee = 2500; // Frais de manutention fixes en FCFA
        $insuranceFee = $declaredValue > 0 ? $declaredValue * 0.02 : 0; // 2% d'assurance sur la valeur en FCFA
        $otherFees = 1000; // Autres frais fixes en FCFA
        $cartonsCost = $shipmentData['cartons_cost'] ?? 0; // Déjà en FCFA

        // Calcul du total
        $totalAmount = $shippingCost + $customsFee + $handlingFee + $insuranceFee + $otherFees + $cartonsCost;

        // Génération du numéro de facture
        $invoiceNumber = 'INV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));

        // Création de la facture
        $invoice = Invoice::create([
            'invoice_number' => $invoiceNumber,
            'parcel_id' => $parcel->id,
            'user_id' => $parcel->user_id,
            'shipping_cost' => $shippingCost,
            'customs_fee' => $customsFee,
            'handling_fee' => $handlingFee,
            'insurance_fee' => $insuranceFee,
            'other_fees' => $otherFees,
            'cartons_cost' => $cartonsCost,
            'subtotal' => $totalAmount,
            'tax_amount' => 0,
            'total_amount' => $totalAmount,
            'currency' => 'XOF',
            'status' => 'draft',
            'issue_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(15)->format('Y-m-d'),
            'notes' => 'Facture générée automatiquement depuis la déclaration du colis ' . $parcel->tracking_number,
        ]);

        return $invoice;
    }
}
