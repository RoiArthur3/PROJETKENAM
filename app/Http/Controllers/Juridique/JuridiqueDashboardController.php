<?php

namespace App\Http\Controllers\Juridique;

use App\Http\Controllers\Controller;
use App\Models\FinancementDossier;
use App\Models\FinancementEcheance;
use App\Models\FinancementOffreBancaire;
use App\Models\JuridiqueContrat;
use App\Models\JuridiqueDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class JuridiqueDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Vérifier si les tables existent
        $hasContratsTable = Schema::hasTable('juridique_contrats');
        $hasDocumentsTable = Schema::hasTable('juridique_documents');
        $hasDossiersTable = Schema::hasTable('financement_dossiers');
        $hasOffresTable = Schema::hasTable('financement_offres_bancaires');
        $hasEcheancesTable = Schema::hasTable('financement_echeances');

        // Statistiques dynamiques
        $stats = [
            'contrats' => $hasContratsTable ? JuridiqueContrat::count() : 0,
            'documents' => $hasDocumentsTable ? JuridiqueDocument::count() : 0,
            'dossiers' => $hasDossiersTable ? FinancementDossier::count() : 0,
            'offres' => $hasOffresTable ? FinancementOffreBancaire::count() : 0,
            'echeances' => $hasEcheancesTable ? FinancementEcheance::count() : 0,
            'montant_demande' => $hasDossiersTable ? FinancementDossier::sum('montant_demande') : 0,
            'montant_obtenu' => $hasOffresTable ? FinancementOffreBancaire::where('statut', 'acceptee')->sum('montant_propose') : 0,
        ];

        // Données récentes
        $contratsRecents = $hasContratsTable ? JuridiqueContrat::latest()->take(5)->get() : collect();
        $dossiersRecents = $hasDossiersTable ? FinancementDossier::latest()->take(5)->get() : collect();
        $echeancesProches = $hasEcheancesTable ? FinancementEcheance::where('statut', 'a_payer')
            ->orderBy('date_echeance', 'asc')
            ->take(5)->get() : collect();

        // Calcul des taux
        $tauxAcceptation = $stats['offres'] > 0 ?
            round(($hasOffresTable ? FinancementOffreBancaire::where('statut', 'acceptee')->count() : 0) / $stats['offres'] * 100, 1) : 0;

        // Interactions avec d'autres modules
        $interactions = [
            'contrats_expirant_bientot' => $hasContratsTable ? JuridiqueContrat::expirantBientot()->count() : 0,
            'documents_expirant_bientot' => $hasDocumentsTable ? JuridiqueDocument::expirantBientot()->count() : 0,
            'echeances_en_retard' => $hasEcheancesTable ? FinancementEcheance::enRetard()->count() : 0,
            'dossiers_en_cours' => $hasDossiersTable ? FinancementDossier::enCours()->count() : 0,
            'utilisateurs_actifs' => User::where('is_active', true)->count(),
        ];

        // Alertes importantes
        $alertes = [
            'contrats_expires' => $hasContratsTable ? JuridiqueContrat::where('date_fin', '<', now())->count() : 0,
            'documents_expires' => $hasDocumentsTable ? JuridiqueDocument::expires()->count() : 0,
            'echeances_impayees' => $hasEcheancesTable ? FinancementEcheance::enRetard()->count() : 0,
        ];

        return view('juridique.dashboard', compact(
            'stats',
            'contratsRecents',
            'dossiersRecents',
            'echeancesProches',
            'tauxAcceptation',
            'interactions',
            'alertes'
        ));
    }
}
