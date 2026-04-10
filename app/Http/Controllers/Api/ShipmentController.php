<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ShipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->user()->isClient()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé',
            ], 403);
        }

        $query = Shipment::withCount('parcels');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('transport_mode')) {
            $query->where('transport_mode', $request->transport_mode);
        }

        $shipments = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $shipments,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!$request->user()->isAdmin() && !$request->user()->isWarehouse()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'transport_mode' => 'required|in:air_normal,air_express,sea',
            'origin_country' => 'required|string',
            'origin_warehouse' => 'required|string',
            'destination_country' => 'required|string',
            'destination_warehouse' => 'nullable|string',
            'status' => 'nullable|in:pending,in_transit,arrived,completed',
            'departure_date' => 'nullable|date',
            'arrival_date' => 'nullable|date',
            'estimated_arrival_date' => 'nullable|date',
            'carrier_name' => 'nullable|string',
            'tracking_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $shipment = Shipment::create([
            'reference' => $this->generateReference(),
            'transport_mode' => $request->transport_mode,
            'origin_country' => $request->origin_country,
            'origin_warehouse' => $request->origin_warehouse,
            'destination_country' => $request->destination_country,
            'destination_warehouse' => $request->destination_warehouse,
            'status' => $request->status ?? 'pending',
            'departure_date' => $request->departure_date,
            'arrival_date' => $request->arrival_date,
            'estimated_arrival_date' => $request->estimated_arrival_date,
            'carrier_name' => $request->carrier_name,
            'tracking_number' => $request->tracking_number,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lot de groupage créé avec succès',
            'data' => $shipment,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $shipment = Shipment::with(['parcels.user'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $shipment,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (!$request->user()->isAdmin() && !$request->user()->isWarehouse()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé',
            ], 403);
        }

        $shipment = Shipment::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'nullable|in:pending,in_transit,arrived,completed',
            'departure_date' => 'nullable|date',
            'arrival_date' => 'nullable|date',
            'estimated_arrival_date' => 'nullable|date',
            'carrier_name' => 'nullable|string',
            'tracking_number' => 'nullable|string',
            'notes' => 'nullable|string',
            'destination_warehouse' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $shipment->update($request->only([
            'status',
            'departure_date',
            'arrival_date',
            'estimated_arrival_date',
            'carrier_name',
            'tracking_number',
            'notes',
            'destination_warehouse',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Lot de groupage mis à jour',
            'data' => $shipment,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $request = request();

        if (!$request->user() || !$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé',
            ], 403);
        }

        $shipment = Shipment::findOrFail($id);
        $shipment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lot de groupage supprimé',
        ]);
    }

    private function generateReference(): string
    {
        do {
            $reference = 'SHP' . strtoupper(Str::random(10));
        } while (Shipment::where('reference', $reference)->exists());

        return $reference;
    }
}
