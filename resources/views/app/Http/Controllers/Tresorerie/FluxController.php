<?php

namespace App\Http\Controllers\Tresorerie;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FluxFinancier;
use App\Models\CompteBancaire;
use Carbon\Carbon;

class FluxController extends Controller
{
    /**
     * Affiche la liste des flux financiers
     */
    public function index()
    {
        // Récupérer les flux financiers (à remplacer par votre logique métier)
        $flux = collect([
            (object)[
                'id' => 1,
                'type' => 'entrant',
                'montant' => 1500000,
                'date_operation' => now()->subDays(2),
                'libelle' => 'Virement client',
                'source' => 'Client ABC',
                'compte' => 'BGCI - Compte Principal',
                'compte_bancaire_id' => 1,
                'statut' => 'effectue',
                'categorie' => 'vente',
                'description' => 'Paiement pour la vente #12345',
                'created_at' => now()->subDays(2)
            ],
            (object)[
                'id' => 2,
                'type' => 'sortant',
                'montant' => 750000,
                'date_operation' => now()->subDay(),
                'libelle' => 'Paiement fournisseur',
                'source' => 'Fournisseur XYZ',
                'compte' => 'ECOBANK - Compte Opérations',
                'compte_bancaire_id' => 1,
                'statut' => 'effectue',
                'categorie' => 'achat',
                'description' => 'Paiement des fournitures de bureau',
                'created_at' => now()->subDay()
            ]
        ]);

        // Récupérer les comptes bancaires pour les filtres
        $comptes = CompteBancaire::with('banque')
            ->where('est_actif', true)
            ->orderBy('banque_id')
            ->orderBy('intitule_compte')
            ->get();

        // Calculer les totaux pour les KPI
        $totalEntrees = $flux->where('type', 'entrant')->sum('montant');
        $totalSorties = $flux->where('type', 'sortant')->sum('montant');
        $soldeActuel = $totalEntrees - $totalSorties;

        // Préparer les données pour le graphique
        $fluxMensuels = $flux->groupBy(function($item) {
            return Carbon::parse($item->date_operation)->format('Y-m');
        })->map(function($monthFlux) {
            return [
                'entrees' => $monthFlux->where('type', 'entrant')->sum('montant'),
                'sorties' => $monthFlux->where('type', 'sortant')->sum('montant')
            ];
        });

        return view('tresorerie.flux', compact(
            'flux',
            'comptes',
            'totalEntrees',
            'totalSorties',
            'soldeActuel',
            'fluxMensuels'
        ));
    }
}
