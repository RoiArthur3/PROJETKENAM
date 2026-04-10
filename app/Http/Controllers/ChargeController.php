<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChargeController extends Controller
{
    public function create()
    {
        return view('comptabilite.charges-create');
    }

    public function edit($id)
    {
        return view('comptabilite.charges-edit', compact('id'));
    }

    public function index()
    {
        try {
            // Récupérer les statistiques dynamiques des charges
            $totalCharges = DB::table('depenses')->sum('montant') ?? 0;
            $chargesMois = DB::table('depenses')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('montant') ?? 0;
            
            // Calculer la moyenne mensuelle (total charges / nombre de mois)
            $monthsWithData = DB::table('depenses')
                ->selectRaw('COUNT(DISTINCT MONTH(created_at)) as months')
                ->whereYear('created_at', now()->year)
                ->first();
            $monthsCount = max($monthsWithData->months ?? 1, 1);
            $moyenneMensuelle = $totalCharges / $monthsCount;
            
            // Charges de l'année
            $chargesAnnee = DB::table('depenses')
                ->whereYear('created_at', now()->year)
                ->sum('montant') ?? 0;

            $stats = [
                'total_charges' => $totalCharges,
                'charges_mois' => $chargesMois,
                'moyenne_mensuelle' => $moyenneMensuelle,
                'charges_annee' => $chargesAnnee,
            ];

            return view('comptabilite.charges', compact('stats'));
        } catch (\Exception $e) {
            return view('comptabilite.charges', ['stats' => [
                'total_charges' => 0,
                'charges_mois' => 0,
                'moyenne_mensuelle' => 0,
                'charges_annee' => 0,
            ]])->with('error', 'Erreur lors du chargement des charges: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'libelle' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'date' => 'required|date',
            'categorie' => 'required|string',
            'mode_paiement' => 'required|string',
            'fournisseur' => 'nullable|string',
            'reference' => 'nullable|string',
            'description' => 'nullable|string',
            'statut' => 'required|string',
        ]);

        // Logique pour sauvegarder la charge (à implémenter avec la base de données)

        return response()->json([
            'success' => true,
            'message' => 'Charge ajoutée avec succès',
            'data' => $data
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'libelle' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'date' => 'required|date',
            'categorie' => 'required|string',
            'mode_paiement' => 'required|string',
            'fournisseur' => 'nullable|string',
            'reference' => 'nullable|string',
            'description' => 'nullable|string',
            'statut' => 'required|string',
        ]);

        // Logique pour mettre à jour la charge

        return response()->json([
            'success' => true,
            'message' => 'Charge mise à jour avec succès',
            'data' => $data
        ]);
    }

    public function destroy($id)
    {
        // Logique pour supprimer la charge

        return response()->json([
            'success' => true,
            'message' => 'Charge supprimée avec succès'
        ]);
    }

    public function getStatistics()
    {
        // Statistiques des charges (données d'exemple)
        $stats = [
            'total_charges' => 12500000,
            'charges_mois' => 3500000,
            'charges_annee' => 42000000,
            'moyenne_mensuelle' => 3500000,
            'par_categorie' => [
                'Loyer' => 1500000,
                'Salaires' => 8000000,
                'Fournitures' => 2000000,
                'Services' => 1000000,
            ],
            'evolution_mensuelle' => [
                'Jan' => 3200000,
                'Fev' => 3400000,
                'Mar' => 3500000,
                'Avr' => 3300000,
                'Mai' => 3600000,
                'Jun' => 3500000,
            ]
        ];

        return response()->json($stats);
    }

    public function exportPDF(Request $request)
    {
        $filters = $request->all();

        // Logique pour générer le PDF des charges
        return view('comptabilite.charges-pdf', compact('filters'));
    }

    public function exportExcel(Request $request)
    {
        $filters = $request->all();

        // Logique pour générer l'export Excel
        $csvContent = "Libellé,Montant,Date,Catégorie,Mode paiement,Fournisseur,Statut\n";

        // Données d'exemple
        $charges = [
            ['Loyer bureau', 1500000, '2024-01-01', 'Loyer', 'Virement', 'SCI KENAM', 'Payé'],
            ['Salaires Janvier', 8000000, '2024-01-31', 'Salaires', 'Virement', 'Divers', 'Payé'],
            ['Fournitures bureau', 200000, '2024-01-15', 'Fournitures', 'Espèces', 'Papeterie ABC', 'Payé'],
        ];

        foreach ($charges as $charge) {
            $csvContent .= implode(',', $charge) . "\n";
        }

        $filename = 'charges_' . date('Y-m-d') . '.csv';

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"$filename\"");
    }
}
