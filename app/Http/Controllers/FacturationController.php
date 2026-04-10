<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FacturationController extends Controller
{
    public function index()
    {
        try {
            // Récupérer les statistiques dynamiques des factures
            $totalFactures = DB::table('factures')->count();
            $montantTotal = DB::table('factures')->sum('montant_ttc') ?? 0;
            $facturesPayees = DB::table('factures')->where('statut', 'payee')->count();
            $facturesImpayees = DB::table('factures')->where('statut', 'impayee')->count();
            $facutresEnAttente = DB::table('factures')->where('statut', 'en_attente')->count();
            $montantEnAttente = DB::table('factures')->where('statut', 'en_attente')->sum('montant_ttc') ?? 0;

            $stats = [
                'total_factures' => $totalFactures,
                'montant_total' => $montantTotal,
                'factures_payees' => $facturesPayees,
                'factures_impayees' => $facturesImpayees,
                'factures_en_attente' => $facutresEnAttente,
                'montant_en_attente' => $montantEnAttente,
            ];

            return view('comptabilite.facturation', compact('stats'));
        } catch (\Exception $e) {
            return view('comptabilite.facturation', ['stats' => [
                'total_factures' => 0,
                'montant_total' => 0,
                'factures_payees' => 0,
                'factures_impayees' => 0,
                'factures_en_attente' => 0,
                'montant_en_attente' => 0,
            ]])->with('error', 'Erreur lors du chargement des statistiques: ' . $e->getMessage());
        }
    }

    public function etatsFactures()
    {
        // Récupérer toutes les factures avec leurs relations
        $factures = DB::table('factures')
            ->leftJoin('clients', 'factures.client_id', '=', 'clients.id')
            ->leftJoin('projets', 'factures.projet_id', '=', 'projets.id')
            ->leftJoin('bons_commande', 'factures.bon_commande_id', '=', 'bons_commande.id')
            ->select(
                'factures.*',
                'clients.nom_complet as client_nom',
                'clients.code_client',
                'projets.nom_projet',
                'projets.code_projet',
                'bons_commande.numero_bc'
            )
            ->orderBy('factures.date_facturation', 'desc')
            ->get();

        // Calculs des totaux
        $totalFactures = $factures->count();
        $totalMontantHT = $factures->sum('montant_ht');
        $totalMontantTVA = $factures->sum('montant_tva');
        $totalMontantTTC = $factures->sum('montant_ttc');
        $totalAcomptes = $factures->sum('acompte');
        $totalRestant = $factures->sum('montant_restant');

        // Statistiques par statut
        $statsParStatut = $factures->groupBy('statut')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total_ttc' => $group->sum('montant_ttc')
            ];
        });

        return view('comptabilite.etats-factures', compact(
            'factures',
            'totalFactures',
            'totalMontantHT',
            'totalMontantTVA',
            'totalMontantTTC',
            'totalAcomptes',
            'totalRestant',
            'statsParStatut'
        ));
    }

    public function create()
    {
        // Récupérer les données pour les listes déroulantes
        $clients = DB::table('clients')->where('est_actif', true)->orderBy('nom_complet')->get();
        $projets = DB::table('projets')->where('statut', '!=', 'annule')->orderBy('nom_projet')->get();
        $bonsCommande = DB::table('bons_commande')
            ->whereIn('statut', ['accepte', 'envoye'])
            ->orderBy('numero_bc')
            ->get();

        return view('comptabilite.facturation-create', compact('clients', 'projets', 'bonsCommande'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'numero_facture' => 'required|string|max:255|unique:factures,numero_facture',
            'numero_fne' => 'nullable|string|max:255',
            'client_id' => 'nullable|integer|exists:clients,id',
            'client_nom' => 'required|string|max:255',
            'bon_commande_id' => 'nullable|integer|exists:bons_commande,id',
            'bon_commande_numero' => 'nullable|string|max:255',
            'projet_id' => 'nullable|integer|exists:projets,id',
            'projet_nom' => 'nullable|string|max:255',
            'mission' => 'nullable|string|max:500',
            'designation' => 'required|string',
            'montant_ht' => 'required|numeric|min:0',
            'tva_taux' => 'required|numeric|min:0|max:100',
            'acompte_paye' => 'nullable|numeric|min:0',
            'date_echeance' => 'nullable|date|after_or_equal:today',
            'mode_paiement' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        // Calculs automatiques
        $montant_tva = $data['montant_ht'] * ($data['tva_taux'] / 100);
        $montant_ttc = $data['montant_ht'] + $montant_tva;
        $acompte = $data['acompte_paye'] ?? 0;
        $montant_restant = $montant_ttc - $acompte;

        // Insertion de la facture
        $factureId = DB::table('factures')->insertGetId([
            'numero_facture' => $data['numero_facture'],
            'numero_fne' => $data['numero_fne'] ?? null,
            'date_facturation' => Carbon::now()->toDateString(), // Date automatique
            'date_depot' => null, // Vide par défaut
            'client_id' => $data['client_id'],
            'client_nom' => $data['client_nom'],
            'bon_commande_id' => $data['bon_commande_id'],
            'bon_commande_numero' => $data['bon_commande_numero'],
            'projet_id' => $data['projet_id'],
            'projet_nom' => $data['projet_nom'],
            'mission' => $data['mission'] ?? null,
            'designation' => $data['designation'],
            'montant_ht' => $data['montant_ht'],
            'tva_taux' => $data['tva_taux'],
            'montant_tva' => $montant_tva,
            'montant_ttc' => $montant_ttc,
            'acompte' => $acompte,
            'montant_restant' => $montant_restant,
            'statut' => $acompte >= $montant_ttc ? 'payee' : ($acompte > 0 ? 'partiellement_payee' : 'en_attente'),
            'date_echeance' => $data['date_echeance'],
            'mode_paiement' => $data['mode_paiement'],
            'notes' => $data['notes'],
            'created_by' => auth()->user()->name ?? 'system',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('facturation.etats')
            ->with('success', 'Facture #' . $data['numero_facture'] . ' créée avec succès');
    }

    public function show($id)
    {
        $facture = DB::table('factures')
            ->leftJoin('clients', 'factures.client_id', '=', 'clients.id')
            ->leftJoin('projets', 'factures.projet_id', '=', 'projets.id')
            ->leftJoin('bons_commande', 'factures.bon_commande_id', '=', 'bons_commande.id')
            ->select(
                'factures.*',
                'clients.nom_complet as client_nom',
                'clients.telephone as client_telephone',
                'clients.email as client_email',
                'clients.adresse as client_adresse',
                'projets.nom_projet',
                'projets.code_projet',
                'bons_commande.numero_bc'
            )
            ->where('factures.id', $id)
            ->first();

        if (!$facture) {
            return redirect()->route('facturation.etats')->with('error', 'Facture non trouvée');
        }

        return view('comptabilite.facturation-show', compact('facture'));
    }

    public function edit($id)
    {
        $facture = DB::table('factures')->where('id', $id)->first();

        if (!$facture) {
            return redirect()->route('facturation.etats')->with('error', 'Facture non trouvée');
        }

        $clients = DB::table('clients')->where('est_actif', true)->orderBy('nom_complet')->get();
        $projets = DB::table('projets')->where('statut', '!=', 'annule')->orderBy('nom_projet')->get();
        $bonsCommande = DB::table('bons_commande')
            ->whereIn('statut', ['accepte', 'envoye'])
            ->orderBy('numero_bc')
            ->get();

        return view('comptabilite.facturation-edit', compact('facture', 'clients', 'projets', 'bonsCommande'));
    }

    public function update(Request $request, $id)
    {
        $facture = DB::table('factures')->where('id', $id)->first();

        if (!$facture) {
            return redirect()->route('facturation.etats')->with('error', 'Facture non trouvée');
        }

        $data = $request->validate([
            'numero_facture' => 'required|string|max:255|unique:factures,numero_facture,' . $id,
            'client_id' => 'nullable|integer|exists:clients,id',
            'client_nom' => 'required|string|max:255',
            'bon_commande_id' => 'nullable|integer|exists:bons_commande,id',
            'bon_commande_numero' => 'nullable|string|max:255',
            'projet_id' => 'nullable|integer|exists:projets,id',
            'projet_nom' => 'nullable|string|max:255',
            'designation' => 'required|string',
            'montant_ht' => 'required|numeric|min:0',
            'tva_taux' => 'required|numeric|min:0|max:100',
            'acompte' => 'nullable|numeric|min:0',
            'date_echeance' => 'nullable|date|after_or_equal:today',
            'mode_paiement' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        // Calculs automatiques
        $montant_tva = $data['montant_ht'] * ($data['tva_taux'] / 100);
        $montant_ttc = $data['montant_ht'] + $montant_tva;
        $acompte = $data['acompte'] ?? 0;
        $montant_restant = $montant_ttc - $acompte;

        // Mise à jour de la facture
        DB::table('factures')->where('id', $id)->update([
            'numero_facture' => $data['numero_facture'],
            'client_id' => $data['client_id'],
            'client_nom' => $data['client_nom'],
            'bon_commande_id' => $data['bon_commande_id'],
            'bon_commande_numero' => $data['bon_commande_numero'],
            'projet_id' => $data['projet_id'],
            'projet_nom' => $data['projet_nom'],
            'designation' => $data['designation'],
            'montant_ht' => $data['montant_ht'],
            'tva_taux' => $data['tva_taux'],
            'montant_tva' => $montant_tva,
            'montant_ttc' => $montant_ttc,
            'acompte' => $acompte,
            'montant_restant' => $montant_restant,
            'statut' => $acompte >= $montant_ttc ? 'payee' : ($acompte > 0 ? 'partiellement_payee' : 'en_attente'),
            'date_echeance' => $data['date_echeance'],
            'mode_paiement' => $data['mode_paiement'],
            'notes' => $data['notes'],
            'updated_by' => auth()->user()->name ?? 'system',
            'updated_at' => now()
        ]);

        return redirect()->route('facturation.etats')
            ->with('success', 'Facture #' . $data['numero_facture'] . ' mise à jour avec succès');
    }

    public function depot(Request $request, $id)
    {
        $request->validate([
            'date_depot' => 'required|date'
        ]);

        DB::table('factures')->where('id', $id)->update([
            'date_depot' => $request->date_depot,
            'updated_at' => now()
        ]);

        return back()->with('success', 'Date de dépôt enregistrée avec succès');
    }

    public function destroy($id)
    {
        $facture = DB::table('factures')->where('id', $id)->first();

        if (!$facture) {
            return redirect()->route('facturation.etats')->with('error', 'Facture non trouvée');
        }

        DB::table('factures')->where('id', $id)->delete();

        return redirect()->route('facturation.etats')
            ->with('success', 'Facture #' . $facture->numero_facture . ' supprimée avec succès');
    }
}
