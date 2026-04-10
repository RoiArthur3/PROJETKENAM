<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CurrencyService;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    /**
     * Obtenir toutes les devises disponibles
     */
    public function getCurrencies()
    {
        $currencies = CurrencyService::getAvailableCurrencies();

        return response()->json([
            'success' => true,
            'data' => $currencies
        ]);
    }

    /**
     * Convertir un montant
     */
    public function convert(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0',
            'from_currency' => 'required|string|size:3',
            'to_currency' => 'required|string|size:3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = CurrencyService::convertAndFormat(
            $request->amount,
            strtoupper($request->from_currency),
            strtoupper($request->to_currency)
        );

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    /**
     * Obtenir les taux de change
     */
    public function getRates(Request $request)
    {
        $fromCurrency = $request->get('from_currency', 'EUR');

        $rates = ExchangeRate::where('from_currency', strtoupper($fromCurrency))
            ->where('is_active', true)
            ->get()
            ->map(function ($rate) {
                return [
                    'to_currency' => $rate->to_currency,
                    'rate' => $rate->rate,
                    'info' => CurrencyService::getCurrencyInfo($rate->to_currency)
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'from_currency' => strtoupper($fromCurrency),
                'from_info' => CurrencyService::getCurrencyInfo($fromCurrency),
                'rates' => $rates
            ]
        ]);
    }

    /**
     * Calculer le total dans différentes devises
     */
    public function calculateTotal(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'amounts' => 'required|array',
            'amounts.*' => 'numeric|min:0',
            'base_currency' => 'required|string|size:3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = CurrencyService::calculateTotalInCurrencies(
            $request->amounts,
            strtoupper($request->base_currency)
        );

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }
}
