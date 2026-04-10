<?php

namespace App\Http\Controllers;

use App\Models\Devis;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DevisController extends Controller
{
    public function index()
    {
        $devis = Devis::orderByDesc('issue_date')->limit(200)->get();
        return view('commercial.proforma', compact('devis'));
    }

    public function update(Request $request, $id)
    {
        // Validation des données
        $validatedData = $request->validate([
            'client_id' => 'required|integer',
            'objet' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'issue_date' => 'required|date',
            'due_date' => 'nullable|date',
            'statut' => 'required|in:brouillon,en_attente,accepte,refuse',
            'prestations' => 'required|array|min:1',
            'prestations.*.description' => 'required|string|max:255',
            'prestations.*.quantite' => 'required|integer|min:1',
            'prestations.*.prix_unitaire' => 'required|numeric|min:0',
            'prestations.*.tva' => 'required|numeric|min:0|max:100',
            'prestations.*.details' => 'nullable|string'
        ]);

        // Simulation de mise à jour (remplacer par sauvegarde en base)
        // Pour la démo, on stocke temporairement en session
        $devisData = [
            'id' => $id,
            'reference' => 'DEV-' . str_pad($id, 4, '0', STR_PAD_LEFT),
            'client_id' => $validatedData['client_id'],
            'client_name' => ['TechnoPlus SA', 'Logistics Pro', 'Energy Solutions'][$validatedData['client_id'] - 1] ?? 'Client Inconnu',
            'objet' => $validatedData['objet'],
            'notes' => $validatedData['notes'],
            'issue_date' => $validatedData['issue_date'],
            'due_date' => $validatedData['due_date'],
            'statut' => $validatedData['statut'],
            'prestations' => array_values($validatedData['prestations']), // Réindexer le tableau
            'updated_at' => now()
        ];

        // Stocker temporairement en session pour la démonstration
        session(['devis_' . $id => $devisData]);

        return redirect()->route('proforma.show', $id)
            ->with('success', 'Proforma mise à jour avec succès !');
    }

    public function duplicate(Request $request)
    {
        try {
            $data = $request->all();

            // Générer un nouvel ID pour la duplication
            $newId = rand(1000, 9999);
            $data['id'] = $newId;
            $data['reference'] = 'DEV-' . str_pad($newId, 4, '0', STR_PAD_LEFT);
            $data['statut'] = 'brouillon'; // Les duplicatas sont en brouillon
            $data['issue_date'] = now()->format('Y-m-d');
            $data['due_date'] = now()->addDays(30)->format('Y-m-d');

            // Stocker en session
            session(['devis_' . $newId => $data]);

            return response()->json([
                'success' => true,
                'message' => 'Proforma dupliquée avec succès',
                'new_id' => $newId,
                'reference' => $data['reference']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la duplication: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadPDF($id)
    {
        // Simulation des données pour la proforma (remplacer par récupération en base)
        $devis = (object) [
            'id' => $id,
            'reference' => 'DEV-' . str_pad($id, 4, '0', STR_PAD_LEFT),
            'client_name' => 'TechnoPlus SA',
            'client_address' => '123 Rue de la Paix, Abidjan, Côte d\'Ivoire',
            'issue_date' => now(),
            'due_date' => now()->addDays(30),
            'objet' => 'Maintenance préventive et corrective des équipements informatiques',
            'montant_ht' => 2500000,
            'tva' => 450000,
            'total_ttc' => 2950000,
            'statut' => 'en_attente',
            'prestations' => [
                [
                    'numero' => 1,
                    'description' => 'Maintenance préventive mensuelle',
                    'details' => 'Visites techniques programmées avec contrôles complets des équipements',
                    'quantite' => 12,
                    'prix_unitaire' => 150000,
                    'tva' => 18,
                    'total_ht' => 1800000
                ],
                [
                    'numero' => 2,
                    'description' => 'Réparation d\'urgence',
                    'details' => 'Interventions d\'urgence 24/7 pour pannes critiques',
                    'quantite' => 1,
                    'prix_unitaire' => 500000,
                    'tva' => 18,
                    'total_ht' => 500000
                ],
                [
                    'numero' => 3,
                    'description' => 'Pièces de rechange',
                    'details' => 'Fourniture de composants et pièces détachées',
                    'quantite' => 1,
                    'prix_unitaire' => 200000,
                    'tva' => 18,
                    'total_ht' => 200000
                ]
            ]
        ];

        try {
            $pdf = Pdf::loadView('commercial.devis.pdf', compact('devis'));
            $filename = 'proforma_' . $devis->reference . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la génération du PDF: ' . $e->getMessage());
        }
    }
}
