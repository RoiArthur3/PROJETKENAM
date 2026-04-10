<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckOperationAmount
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Récupérer le montant de la requête
        $montant = $this->getMontantFromRequest($request);

        if ($montant && $montant >= 1000000) {
            // Ajouter un message flash pour alerter l'utilisateur
            Session::flash('warning', [
                'title' => 'Montant Élevé Détecté',
                'message' => 'Cette opération dépasse le seuil de 1.000.000 FCFA et nécessitera une validation du DG et Manager.',
                'icon' => 'fas fa-exclamation-triangle',
                'timeout' => 5000
            ]);

            // Ajouter les données à la requête pour le traitement
            $request->merge([
                'requires_special_validation' => true,
                'validation_threshold' => 1000000,
                'excess_amount' => $montant - 1000000
            ]);
        }

        return $next($request);
    }

    /**
     * Extraire le montant de la requête selon la méthode
     */
    private function getMontantFromRequest(Request $request): ?float
    {
        $montant = null;

        // Vérifier dans les données de la requête
        if ($request->has('montant')) {
            $montant = is_numeric($request->input('montant'))
                ? (float) $request->input('montant')
                : $this->parseAmount($request->input('montant'));
        }

        // Vérifier dans les données JSON pour les requêtes API
        if ($request->isJson() && $request->has('montant')) {
            $montant = is_numeric($request->json('montant'))
                ? (float) $request->json('montant')
                : $this->parseAmount($request->json('montant'));
        }

        // Vérifier dans le corps de la requête pour PUT/PATCH
        if (in_array($request->method(), ['PUT', 'PATCH']) && $request->getContent()) {
            $data = json_decode($request->getContent(), true);
            if (isset($data['montant'])) {
                $montant = is_numeric($data['montant'])
                    ? (float) $data['montant']
                    : $this->parseAmount($data['montant']);
            }
        }

        return $montant;
    }

    /**
     * Parser un montant formaté (ex: "1 000 000" ou "1,000,000")
     */
    private function parseAmount($amount): ?float
    {
        if (!is_string($amount)) {
            return null;
        }

        // Nettoyer le montant
        $clean = preg_replace('/[^0-9.,]/', '', $amount);

        // Remplacer les virgules par des points pour la conversion
        $clean = str_replace(',', '.', $clean);

        // Supprimer les milliers (points) si nécessaire
        if (substr_count($clean, '.') > 1) {
            $parts = explode('.', $clean);
            $clean = implode('', array_slice($parts, 0, -1)) . '.' . end($parts);
        }

        return is_numeric($clean) ? (float) $clean : null;
    }
}
