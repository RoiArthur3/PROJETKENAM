<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FactureComptableController extends Controller
{
    public function index()
    {
        return view('comptabilite.factures');
    }

    public function create()
    {
        return view('comptabilite.factures-create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:client,fournisseur',
            'numero' => 'required|string|max:50',
            'date_emission' => 'required|date',
            'date_echeance' => 'required|date',
            'tiers' => 'required|string|max:255',
            'montant_ht' => 'required|numeric|min:0',
            'tva' => 'required|numeric|min:0',
            'montant_ttc' => 'required|numeric|min:0',
            'statut' => 'required|in:paye,partiellement_paye,impayee,annulee',
            'description' => 'nullable|string'
        ]);

        // Logique de stockage (à implémenter avec la base de données)

        return response()->json([
            'success' => true,
            'message' => 'Facture créée avec succès',
            'data' => $data
        ]);
    }

    public function show($id)
    {
        return view('comptabilite.factures-show', compact('id'));
    }

    public function edit($id)
    {
        return view('comptabilite.factures-edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'type' => 'required|in:client,fournisseur',
            'numero' => 'required|string|max:50',
            'date_emission' => 'required|date',
            'date_echeance' => 'required|date',
            'tiers' => 'required|string|max:255',
            'montant_ht' => 'required|numeric|min:0',
            'tva' => 'required|numeric|min:0',
            'montant_ttc' => 'required|numeric|min:0',
            'statut' => 'required|in:paye,partiellement_paye,impayee,annulee',
            'description' => 'nullable|string'
        ]);

        // Logique de mise à jour (à implémenter avec la base de données)

        return response()->json([
            'success' => true,
            'message' => 'Facture mise à jour avec succès',
            'data' => $data
        ]);
    }

    public function destroy($id)
    {
        // Logique de suppression (à implémenter avec la base de données)

        return response()->json([
            'success' => true,
            'message' => 'Facture supprimée avec succès'
        ]);
    }

    public function getStatistics()
    {
        // Statistiques des factures - DONNÉES DYNAMIQUES DE LA BASE DE DONNÉES
        try {
            // Total factures
            $totalFactures = DB::table('factures')->count();
            
            // Factures par type (clients vs fournisseurs)
            $facturesClients = DB::table('factures')->where('type', 'client')->count();
            $facturesFournisseurs = DB::table('factures')->where('type', 'fournisseur')->count();
            
            // Montants totaux
            $montantTotal = DB::table('factures')->sum('montant_ttc') ?? 0;
            $montantEnAttente = DB::table('factures')->where('statut', 'en_attente')->sum('montant_ttc') ?? 0;
            
            // Factures par statut
            $facturesPayees = DB::table('factures')->where('statut', 'payee')->count();
            $facturesImpayees = DB::table('factures')->where('statut', 'impayee')->count();
            $facturesPartiellemenlPayees = DB::table('factures')->where('statut', 'partiellement_payee')->count();
            
            // Statistiques du mois courant
            $moiscourant = [
                'emises' => DB::table('factures')->whereMonth('date_emission', now()->month)->whereYear('date_emission', now()->year)->count(),
                'payees' => DB::table('factures')->where('statut', 'payee')->whereMonth('date_emission', now()->month)->whereYear('date_emission', now()->year)->count(),
                'impayees' => DB::table('factures')->where('statut', 'impayee')->whereMonth('date_emission', now()->month)->whereYear('date_emission', now()->year)->count(),
            ];

            $stats = [
                'total_factures' => $totalFactures,
                'factures_clients' => $facturesClients,
                'factures_fournisseurs' => $facturesFournisseurs,
                'montant_total' => $montantTotal,
                'montant_en_attente' => $montantEnAttente,
                'factures_payees' => $facturesPayees,
                'factures_impayees' => $facturesImpayees,
                'factures_partiellement_payees' => $facturesPartiellemenlPayees,
                'mois_courant' => $moiscourant
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la récupération des statistiques: ' . $e->getMessage()
            ], 500);
        }
    }
}
