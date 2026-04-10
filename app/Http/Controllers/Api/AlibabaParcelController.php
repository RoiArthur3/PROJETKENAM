<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AlibabaParcel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class AlibabaParcelController extends Controller
{
    /**
     * Create a new Alibaba parcel
     */
    public function store(Request $request)
    {
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé - connexion requise',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'shipment_type' => 'required|in:international,national',
            'transport_mode' => 'required|in:air,sea,road_national,express_national,standard_national',
            'origin_country' => 'required|string|exists:countries,code',
            'origin_city' => 'nullable|exists:cities,id',
            'destination_country' => 'required|string|exists:countries,code',
            'destination_city' => 'nullable|exists:cities,id',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone1' => 'required|string',
            'phone2' => 'nullable|string',
            'fragile' => 'boolean',
            'order_number' => 'nullable|string',
            'purchase_site' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Generate unique tracking number
        $trackingNumber = $this->generateTrackingNumber();

        $alibabaParcel = AlibabaParcel::create([
            'user_id' => $request->user()->id,
            'tracking_number' => $trackingNumber,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'transport_mode' => $request->transport_mode,
            'origin_country' => $request->origin_country,
            'origin_city' => $request->origin_city,
            'destination_country' => $request->destination_country,
            'destination_city' => $request->destination_city,
            'phone1' => $request->phone1,
            'phone2' => $request->phone2,
            'fragile' => $request->boolean('fragile', false),
            'alibaba_order_number' => $request->order_number,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Colis Alibaba créé avec succès',
            'data' => $alibabaParcel,
        ], 201);
    }

    /**
     * Get user's Alibaba parcels
     */
    public function index(Request $request)
    {
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé',
            ], 401);
        }

        $parcels = AlibabaParcel::where('user_id', $request->user()->id)
            ->with(['originCity', 'destinationCity'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $parcels,
        ]);
    }

    /**
     * Generate PDF label for parcel
     */
    public function generatePdf(Request $request, $trackingNumber)
    {
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé',
            ], 401);
        }

        $parcel = AlibabaParcel::where('tracking_number', $trackingNumber)
            ->with(['originCity', 'destinationCity'])
            ->first();

        if (!$parcel) {
            return response()->json([
                'success' => false,
                'message' => 'Colis non trouvé',
            ], 404);
        }

        // Temporairement désactivé - nécessite l'installation de la librairie PDF
        return response()->json([
            'success' => false,
            'message' => 'Génération PDF temporairement désactivée',
        ], 501);

        /*
        $pdf = PDF::loadView('pdf.alibaba-label', [
            'parcel' => $parcel,
            'groupAddress' => $this->getGroupAddress(),
        ]);

        return $pdf->download("label-{$trackingNumber}.pdf");
        */
    }

    /**
     * Track Alibaba parcel
     */
    public function track($trackingNumber)
    {
        $parcel = AlibabaParcel::where('tracking_number', $trackingNumber)->first();

        if (!$parcel) {
            return response()->json([
                'success' => false,
                'message' => 'Colis non trouvé',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $parcel,
        ]);
    }

    private function generateTrackingNumber(): string
    {
        do {
            $trackingNumber = 'GRP' . str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        } while (AlibabaParcel::where('tracking_number', $trackingNumber)->exists());

        return $trackingNumber;
    }

    private function getGroupAddress(): array
    {
        return [
            'company' => 'GROUPAGE INTERNATIONAL',
            'address' => '123 Rue du Commerce',
            'city' => 'Paris',
            'country' => 'France',
            'phone' => '+33 1 23 45 67 89',
            'email' => 'contact@groupage.com',
        ];
    }
}
