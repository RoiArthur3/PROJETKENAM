<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuditController extends Controller
{
    /**
     * Affiche le tableau de bord de l'audit
     */
    public function dashboard()
    {
        // Données factices pour le tableau de bord
        $stats = (object)[
            'controlesEnCours' => 3,
            'rapportsMensuels' => 5,
            'alertesNonTraitees' => 2,
            'tauxConformite' => 87.5
        ];

        $derniersControles = collect([
            (object)[
                'reference' => 'CTRL-' . now()->format('Y') . '-001',
                'type' => 'Interne',
                'departement' => 'Finance',
                'date_debut' => now()->subDays(5),
                'date_fin' => now()->addDays(2),
                'statut' => 'en_cours',
                'score' => null
            ],
            (object)[
                'reference' => 'CTRL-' . now()->format('Y') . '-002',
                'type' => 'Externe',
                'departement' => 'Ressources Humaines',
                'date_debut' => now()->subDays(10),
                'date_fin' => now()->subDays(3),
                'statut' => 'terminé',
                'score' => 92
            ]
        ]);

        return view('audit.dashboard', compact('stats', 'derniersControles'));
    }

    /**
     * Affiche la liste des contrôles d'audit
     */
    public function controles(Request $request)
    {
        // Données factices pour les contrôles
        $controles = collect([
            (object)[
                'id' => 1,
                'reference' => 'CTRL-' . now()->format('Y') . '-001',
                'type' => 'Interne',
                'departement' => 'Finance',
                'date_debut' => now()->subDays(5),
                'date_fin' => now()->addDays(2),
                'auditeur' => 'M. Konan',
                'statut' => 'en_cours',
                'score' => null,
                'objectif' => 'Vérification des procédures comptables',
                'description' => 'Audit des procédures comptables et financières du département Finance pour le trimestre en cours.'
            ],
            (object)[
                'id' => 2,
                'reference' => 'CTRL-' . now()->format('Y') . '-002',
                'type' => 'Externe',
                'departement' => 'Ressources Humaines',
                'date_debut' => now()->subDays(10),
                'date_fin' => now()->subDays(3),
                'auditeur' => 'M. Bamba',
                'statut' => 'terminé',
                'score' => 92,
                'objectif' => 'Audit des processus RH',
                'description' => 'Évaluation des processus de recrutement et de gestion des carrières.'
            ],
            (object)[
                'id' => 3,
                'reference' => 'CTRL-' . now()->format('Y') . '-003',
                'type' => 'Interne',
                'departement' => 'Informatique',
                'date_debut' => now()->subDays(20),
                'date_fin' => now()->subDays(5),
                'auditeur' => 'M. Touré',
                'statut' => 'terminé',
                'score' => 85,
                'objectif' => 'Audit de sécurité informatique',
                'description' => 'Vérification des mesures de sécurité et des accès aux systèmes d\'information.'
            ],
            (object)[
                'id' => 4,
                'reference' => 'CTRL-' . now()->format('Y') . '-004',
                'type' => 'Interne',
                'departement' => 'Logistique',
                'date_debut' => now()->addDays(5),
                'date_fin' => now()->addDays(12),
                'auditeur' => 'Mme Yao',
                'statut' => 'planifié',
                'score' => null,
                'objectif' => 'Audit des stocks',
                'description' => 'Contrôle des procédures de gestion des stocks et des inventaires.'
            ]
        ]);

        // Filtrage des résultats
        if ($request->has('type') && !empty($request->type)) {
            $controles = $controles->where('type', $request->type);
        }

        if ($request->has('departement') && !empty($request->departement)) {
            $controles = $controles->where('departement', $request->departement);
        }

        if ($request->has('statut') && !empty($request->statut)) {
            $controles = $controles->where('statut', $request->statut);
        }

        // Pagination manuelle (simulée)
        $perPage = 10;
        $currentPage = $request->get('page', 1);
        $pagedData = $controles->forPage($currentPage, $perPage);
        $controles = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedData,
            $controles->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('audit.controles.index', compact('controles'));
    }

    /**
     * Affiche le formulaire de création d'un contrôle
     */
    public function createControle()
    {
        return view('audit.controles.create');
    }

    /**
     * Enregistre un nouveau contrôle
     */
    public function storeControle(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:50',
            'departement' => 'required|string|max:100',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'auditeur' => 'required|string|max:100',
            'equipe' => 'nullable|string|max:255',
            'objectif' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document_reference' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:5120',
        ]);

        // En production, on enregistrerait en base de données ici
        // $controle = ControleAudit::create($validated);

        // Traitement du fichier s'il est présent
        if ($request->hasFile('document_reference')) {
            // $path = $request->file('document_reference')->store('audit/documents');
            // $controle->update(['document_path' => $path]);
        }

        return redirect()->route('audit.controles')
                         ->with('success', 'Le contrôle a été créé avec succès.');
    }

    /**
     * Affiche la liste des rapports d'audit
     */
    public function rapports()
    {
        // Données factices pour les rapports
        $rapports = collect([
            (object)[
                'id' => 1,
                'titre' => 'Rapport Trimestriel Finance Q4 2025',
                'type' => 'Trimestriel',
                'periode' => '01/10/2025 - 31/12/2025',
                'date_generation' => now()->subDays(5),
                'statut' => 'généré',
                'fichier' => 'rapport_audit_finance_q4_2025.pdf',
                'taille' => '4.2',
                'score_global' => 85,
                'auditeur' => 'M. Konan',
                'departement' => 'Finance'
            ],
            (object)[
                'id' => 2,
                'titre' => 'Audit Sécurité Informatique Décembre 2025',
                'type' => 'Mensuel',
                'periode' => '01/12/2025 - 31/12/2025',
                'date_generation' => now()->subDays(15),
                'statut' => 'généré',
                'fichier' => 'rapport_audit_si_decembre_2025.pdf',
                'taille' => '3.8',
                'score_global' => 92,
                'auditeur' => 'M. Touré',
                'departement' => 'Informatique'
            ],
            (object)[
                'id' => 3,
                'titre' => 'Rapport Annuel 2025',
                'type' => 'Annuel',
                'periode' => '01/01/2025 - 31/12/2025',
                'date_generation' => now()->subDays(30),
                'statut' => 'généré',
                'fichier' => 'rapport_audit_annuel_2025.pdf',
                'taille' => '8.5',
                'score_global' => 88,
                'auditeur' => 'M. Bamba',
                'departement' => 'Tous départements'
            ],
            (object)[
                'id' => 4,
                'titre' => 'Audit RH Novembre 2025',
                'type' => 'Mensuel',
                'periode' => '01/11/2025 - 30/11/2025',
                'date_generation' => now()->subDays(45),
                'statut' => 'généré',
                'fichier' => 'rapport_audit_rh_novembre_2025.pdf',
                'taille' => '2.9',
                'score_global' => 78,
                'auditeur' => 'Mme Yao',
                'departement' => 'Ressources Humaines'
            ]
        ]);

        return view('audit.rapports', compact('rapports'));
    }

    /**
     * Affiche la liste des alertes d'audit
     */
    public function alertes()
    {
        // Données factices pour les alertes
        $alertes = collect([
            (object)[
                'id' => 1,
                'type' => 'Non-conformité majeure',
                'description' => 'Absence de procédure écrite pour la gestion des accès aux systèmes financiers',
                'gravite' => 'Haute',
                'statut' => 'ouverte',
                'date_creation' => now()->subDays(3),
                'date_echeance' => now()->addDays(7),
                'responsable' => 'Directeur des Systèmes d\'Information',
                'controle_lie' => 'CTRL-' . now()->format('Y') . '-003',
                'actions_correctives' => 'Élaborer et mettre en œuvre une procédure de gestion des accès',
                'delai' => '15 jours'
            ],
            (object)[
                'id' => 2,
                'type' => 'Défaut de contrôle',
                'description' => 'Absence de double validation pour les paiements supérieurs à 5 000 000 FCFA',
                'gravite' => 'Moyenne',
                'statut' => 'en_cours',
                'date_creation' => now()->subDays(10),
                'date_echeance' => now()->addDays(5),
                'responsable' => 'Responsable Comptabilité',
                'controle_lie' => 'CTRL-' . now()->format('Y') . '-001',
                'actions_correctives' => 'Mettre en place un système de double signature pour les paiements importants',
                'delai' => '30 jours'
            ],
            (object)[
                'id' => 3,
                'type' => 'Risque opérationnel',
                'description' => 'Absence de sauvegarde hors site des données critiques',
                'gravite' => 'Critique',
                'statut' => 'en_retard',
                'date_creation' => now()->subDays(45),
                'date_echeance' => now()->subDays(15),
                'responsable' => 'Responsable Infrastructure IT',
                'controle_lie' => 'CTRL-' . now()->format('Y') . '-003',
                'actions_correctives' => 'Mettre en place une solution de sauvegarde externalisée avec test de restauration mensuel',
                'delai' => '30 jours'
            ],
            (object)[
                'id' => 4,
                'type' => 'Amélioration',
                'description' => 'Automatisation possible du processus de rapprochement bancaire',
                'gravite' => 'Basse',
                'statut' => 'fermée',
                'date_creation' => now()->subDays(60),
                'date_echeance' => now()->subDays(30),
                'date_cloture' => now()->subDays(25),
                'responsable' => 'Chef Comptable',
                'controle_lie' => 'CTRL-' . (now()->year - 1) . '-012',
                'actions_correctives' => 'Évaluation des solutions logicielles disponibles et formation du personnel',
                'delai' => '60 jours',
                'commentaire_fermeture' => 'Solution mise en place avec succès. Gain de temps estimé à 8h/semaine.'
            ]
        ]);

        return view('audit.alertes', compact('alertes'));
    }
}
