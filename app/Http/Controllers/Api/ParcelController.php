<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Parcel;
use App\Models\ParcelPhoto;
use App\Models\ParcelStatusHistory;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ParcelController extends Controller
{
    public function index(Request $request)
    {
        $query = Parcel::with(['user', 'shipment', 'photos', 'invoice', 'originCity', 'destinationCity']);

        if ($request->user()->isClient()) {
            $query->where('user_id', $request->user()->id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('transport_mode')) {
            $query->where('transport_mode', $request->transport_mode);
        }

        if ($request->has('tracking_number')) {
            $query->where('tracking_number', 'like', '%' . $request->tracking_number . '%');
        }

        $parcels = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $parcels,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipment_type' => 'required|in:international,national',
            'transport_mode' => 'required|in:air_normal,air_express,sea,road_national,express_national,standard_national',
            'origin_country' => 'required|string|exists:countries,code',
            'origin_city' => 'nullable|exists:cities,id',
            'destination_country' => 'required|string|exists:countries,code',
            'destination_city' => 'nullable|exists:cities,id',
            'content_description' => 'nullable|string',
            'declared_value' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $trackingNumber = $this->generateTrackingNumber();

        $parcel = Parcel::create([
            'tracking_number' => $trackingNumber,
            'user_id' => $request->user()->id,
            'shipment_type' => $request->shipment_type,
            'transport_mode' => $request->transport_mode,
            'origin_country' => $request->origin_country,
            'origin_city' => $request->origin_city,
            'destination_country' => $request->destination_country,
            'destination_city' => $request->destination_city,
            'content_description' => $request->content_description,
            'declared_value' => $request->declared_value,
            'status' => 'announced',
        ]);

        ParcelStatusHistory::create([
            'parcel_id' => $parcel->id,
            'new_status' => 'announced',
            'changed_by' => $request->user()->id,
            'comment' => 'Colis annoncé par le client',
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Colis créé avec succès',
            'data' => $parcel->load(['user', 'statusHistory']),
        ], 201);
    }

    public function show(string $id)
    {
        $parcel = Parcel::with([
            'user',
            'shipment',
            'photos',
            'invoice.payments',
            'statusHistory.changedBy',
            'originCity',
            'destinationCity'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $parcel,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $parcel = Parcel::findOrFail($id);

        if ($request->user()->isClient() && $parcel->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'content_description' => 'nullable|string',
            'declared_value' => 'nullable|numeric|min:0',
            'actual_weight' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $parcel->update($request->only([
            'content_description',
            'declared_value',
            'actual_weight',
            'length',
            'width',
            'height',
        ]));

        if ($request->has(['length', 'width', 'height'])) {
            $parcel->volumetric_weight = $parcel->calculateVolumetricWeight();
            $parcel->chargeable_weight = $parcel->calculateChargeableWeight();
            $parcel->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Colis mis à jour avec succès',
            'data' => $parcel,
        ]);
    }

    public function updateStatus(Request $request, string $id)
    {
        $parcel = Parcel::findOrFail($id);

        if ($request->user()->isClient()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:announced,received,inspected,rejected,grouped,in_transit,arrived,fees_calculated,paid,delivered',
            'comment' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $oldStatus = $parcel->status;
        $parcel->update(['status' => $request->status]);

        ParcelStatusHistory::create([
            'parcel_id' => $parcel->id,
            'old_status' => $oldStatus,
            'new_status' => $request->status,
            'changed_by' => $request->user()->id,
            'comment' => $request->comment,
            'ip_address' => $request->ip(),
        ]);

        // Envoyer une notification SMS si le statut est "received"
        if ($request->status === 'received') {
            try {
                $notificationService = new NotificationService();
                $notificationService->notifyParcelEvent($parcel, 'parcel_received');
            } catch (\Exception $e) {
                Log::error('Failed to send SMS notification', [
                    'parcel_id' => $parcel->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Statut mis à jour avec succès',
            'data' => $parcel->load('statusHistory'),
        ]);
    }

    public function uploadPhotos(Request $request, string $id)
    {
        $parcel = Parcel::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'photos' => 'required|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:5120',
            'type' => 'required|in:package,content',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $uploadedPhotos = [];

        foreach ($request->file('photos') as $index => $photo) {
            $fileName = Str::uuid() . '.' . $photo->getClientOriginalExtension();
            $filePath = $photo->storeAs('parcels/' . $parcel->id, $fileName, 'public');

            $parcelPhoto = ParcelPhoto::create([
                'parcel_id' => $parcel->id,
                'type' => $request->type,
                'file_path' => $filePath,
                'file_name' => $fileName,
                'file_size' => $photo->getSize(),
                'mime_type' => $photo->getMimeType(),
                'order' => $index,
            ]);

            $uploadedPhotos[] = $parcelPhoto;
        }

        return response()->json([
            'success' => true,
            'message' => 'Photos téléchargées avec succès',
            'data' => $uploadedPhotos,
        ]);
    }

    public function tracking(string $trackingNumber)
    {
        $parcel = Parcel::where('tracking_number', $trackingNumber)
            ->with([
                'shipment',
                'photos',
                'invoice',
                'statusHistory.changedBy'
            ])
            ->first();

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

    public function publicStore(Request $request)
    {
        // Vérifier que l'utilisateur est connecté
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé - connexion requise',
            ], 401);
        }

        // Validation simple
        $validator = Validator::make($request->all(), [
            'sender_name' => 'required|string',
            'recipient_name' => 'required|string',
            'transport_mode' => 'required|in:air_normal,air_express,sea',
            'origin_country' => 'required|string',
            'origin_city' => 'nullable|string',
            'destination_country' => 'required|string',
            'destination_city' => 'nullable|string',
            'ship_address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Génération simple du numéro de tracking
        $trackingNumber = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        try {
            $parcel = Parcel::create([
                'tracking_number' => $trackingNumber,
                'user_id' => $request->user()->id, // Utiliser l'utilisateur connecté
                'transport_mode' => $request->transport_mode,
                'origin_country' => $request->origin_country,
                'origin_city' => $request->origin_city ?? null,
                'destination_country' => $request->destination_country,
                'destination_city' => $request->destination_city ?? null,
                'content_description' => $request->content_description ?? null,
                'declared_value' => $request->declared_value ?? null,
                'status' => 'announced',
                'sender_name' => $request->sender_name,
                'sender_phone' => $request->sender_phone ?? null,
                'recipient_name' => $request->recipient_name,
                'recipient_phone' => $request->recipient_phone ?? null,
                'notes' => $request->ship_address ?? null, // Utiliser notes pour ship_address
            ]);

            // Créer l'historique avec l'utilisateur connecté
            ParcelStatusHistory::create([
                'parcel_id' => $parcel->id,
                'new_status' => 'announced',
                'changed_by' => $request->user()->id,
                'comment' => 'Colis annoncé par le client',
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Colis créé avec succès',
                'tracking_number' => $trackingNumber,
                'data' => $parcel,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création: ' . $e->getMessage(),
                'tracking_number' => $trackingNumber,
            ], 500);
        }
    }

    public function destroy(Request $request, string $id)
    {
        $parcel = Parcel::findOrFail($id);

        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé',
            ], 403);
        }

        $parcel->delete();

        return response()->json([
            'success' => true,
            'message' => 'Colis supprimé avec succès',
        ]);
    }

    private function generateTrackingNumber(): string
    {
        do {
            $trackingNumber = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        } while (Parcel::where('tracking_number', $trackingNumber)->exists());

        return $trackingNumber;
    }
}
