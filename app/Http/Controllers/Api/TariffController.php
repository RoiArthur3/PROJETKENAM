<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tariff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TariffController extends Controller
{
    /**
     * Lister tous les tarifs
     */
    public function index(Request $request)
    {
        $query = Tariff::query();

        // Filtrer par pays de destination
        if ($request->has('destination_country')) {
            $query->where('destination_country', 'like', '%' . $request->destination_country . '%');
        }

        // Filtrer par mode de transport
        if ($request->has('transport_mode')) {
            $query->where('transport_mode', $request->transport_mode);
        }

        // Filtrer par statut actif
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $tariffs = $query->orderBy('destination_country')->orderBy('transport_mode')->orderBy('min_weight')->get();

        // Grouper par destination pour un meilleur affichage
        $grouped = $tariffs->groupBy('destination_country');

        return response()->json([
            'success' => true,
            'data' => [
                'grouped' => $grouped,
                'all' => $tariffs
            ]
        ]);
    }

    /**
     * Créer un nouveau tarif
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'transport_mode' => 'required|in:air_normal,air_express,sea',
            'origin_country' => 'required|string',
            'destination_country' => 'required|string',
            'min_weight' => 'required|numeric|min:0',
            'max_weight' => 'nullable|numeric|min:0',
            'price_per_kg' => 'required|numeric|min:0',
            'fixed_fee' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'volumetric_divisor' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $tariff = Tariff::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Tarif créé avec succès',
            'data' => $tariff,
        ], 201);
    }

    /**
     * Afficher un tarif spécifique
     */
    public function show(Tariff $tariff)
    {
        return response()->json([
            'success' => true,
            'data' => $tariff,
        ]);
    }

    /**
     * Mettre à jour un tarif
     */
    public function update(Request $request, Tariff $tariff)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string',
            'transport_mode' => 'in:air_normal,air_express,sea',
            'origin_country' => 'string',
            'destination_country' => 'string',
            'min_weight' => 'numeric|min:0',
            'max_weight' => 'nullable|numeric|min:0',
            'price_per_kg' => 'numeric|min:0',
            'fixed_fee' => 'numeric|min:0',
            'currency' => 'string|max:3',
            'volumetric_divisor' => 'integer|min:1',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $tariff->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Tarif mis à jour avec succès',
            'data' => $tariff,
        ]);
    }

    /**
     * Supprimer un tarif
     */
    public function destroy(Tariff $tariff)
    {
        $tariff->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tarif supprimé avec succès',
        ]);
    }

    /**
     * Activer/Désactiver un tarif
     */
    public function toggleStatus(Tariff $tariff)
    {
        $tariff->is_active = !$tariff->is_active;
        $tariff->save();

        return response()->json([
            'success' => true,
            'message' => 'Tarif ' . ($tariff->is_active ? 'activé' : 'désactivé') . ' avec succès',
            'data' => $tariff,
        ]);
    }

    /**
     * Obtenir les destinations uniques
     */
    public function getDestinations()
    {
        $destinations = Tariff::where('is_active', true)
            ->distinct()
            ->pluck('destination_country')
            ->sort()
            ->values();

        return response()->json([
            'success' => true,
            'data' => $destinations,
        ]);
    }
}
