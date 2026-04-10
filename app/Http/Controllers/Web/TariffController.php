<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Tariff;
use Illuminate\Http\Request;

class TariffController extends Controller
{
    public function index(Request $request)
    {
        $tariffs = Tariff::query()
            ->with(['originCountry', 'destinationCountry'])
            ->when($request->shipment_type, function ($query, $shipmentType) {
                return $query->where('shipment_type', $shipmentType);
            })
            ->when($request->origin_country, function ($query, $originCountry) {
                return $query->where('origin_country', $originCountry);
            })
            ->when($request->destination_country, function ($query, $destinationCountry) {
                return $query->where('destination_country', $destinationCountry);
            })
            ->orderBy('origin_country')
            ->orderBy('destination_country')
            ->orderBy('weight_min')
            ->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $tariffs,
        ]);
    }

    public function getDestinations(Request $request)
    {
        $originCountry = $request->get('origin_country');

        if (!$originCountry) {
            return response()->json([
                'success' => false,
                'message' => 'Pays d\'origine requis',
            ], 422);
        }

        // Destinations internationales depuis le pays d'origine
        $internationalDestinations = Tariff::where('origin_country', $originCountry)
            ->where('shipment_type', 'international')
            ->distinct('destination_country')
            ->pluck('destination_country');

        // Destinations nationales (même pays)
        $nationalDestinations = Tariff::where('origin_country', $originCountry)
            ->where('shipment_type', 'national')
            ->distinct('destination_country')
            ->pluck('destination_country');

        return response()->json([
            'success' => true,
            'data' => [
                'international' => $internationalDestinations->unique()->values(),
                'national' => $nationalDestinations->unique()->values(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'shipment_type' => 'required|in:international,national',
            'origin_country' => 'required|string|exists:countries,code',
            'destination_country' => 'required|string|exists:countries,code',
            'transport_mode' => 'required|in:air_normal,air_express,sea,road_national,express_national,standard_national',
            'weight_min' => 'required|numeric|min:0',
            'weight_max' => 'required|numeric|min:0',
            'base_cost' => 'required|numeric|min:0',
            'cost_per_kg' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $tariff = Tariff::create([
            'shipment_type' => $request->shipment_type,
            'origin_country' => $request->origin_country,
            'destination_country' => $request->destination_country,
            'transport_mode' => $request->transport_mode,
            'weight_min' => $request->weight_min,
            'weight_max' => $request->weight_max,
            'base_cost' => $request->base_cost,
            'cost_per_kg' => $request->cost_per_kg,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tarif créé avec succès',
            'data' => $tariff,
        ], 201);
    }

    public function toggleStatus(Request $request, Tariff $tariff)
    {
        $tariff->update([
            'is_active' => !$tariff->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tarif mis à jour',
            'data' => $tariff,
        ]);
    }
}
