<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * Get cities by country
     */
    public function getByCountry(Request $request)
    {
        $request->validate([
            'country_code' => 'required|string|exists:countries,code',
        ]);

        $country = Country::where('code', $request->country_code)->first();

        if (!$country) {
            return response()->json([
                'success' => false,
                'message' => 'Pays non trouvé',
            ], 404);
        }

        $cities = City::where('country_id', $country->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'latitude', 'longitude']);

        return response()->json([
            'success' => true,
            'data' => $cities,
        ]);
    }

    /**
     * Search cities by name
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2',
            'country_code' => 'nullable|string|exists:countries,code',
        ]);

        $query = City::where('is_active', true)
            ->where('name', 'LIKE', '%' . $request->query . '%')
            ->with('country:id,name,code');

        if ($request->country_code) {
            $country = Country::where('code', $request->country_code)->first();
            if ($country) {
                $query->where('country_id', $country->id);
            }
        }

        $cities = $query->orderBy('name')
            ->limit(20)
            ->get(['id', 'name', 'code', 'country_id']);

        return response()->json([
            'success' => true,
            'data' => $cities,
        ]);
    }

    /**
     * Get all cities (for admin)
     */
    public function index(Request $request)
    {
        $cities = City::with('country:id,name,code')
            ->when($request->country_id, function ($query, $countryId) {
                return $query->where('country_id', $countryId);
            })
            ->when($request->is_active !== null, function ($query, $isActive) {
                return $query->where('is_active', $isActive);
            })
            ->orderBy('country_id')
            ->orderBy('name')
            ->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $cities,
        ]);
    }
}
