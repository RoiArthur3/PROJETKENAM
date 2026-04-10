<?php

namespace App\Http\Controllers;

use App\Models\Controle;
use App\Models\Anomaly;
use App\Models\Inspection;
use App\Models\CorrectiveAction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ControleAuditController extends Controller
{
    public function dashboard(Request $request)
    {
        // Période par défaut : 30 derniers jours
        $dateDebut = $request->get('date_debut', now()->subDays(30)->toDateString());
        $dateFin = $request->get('date_fin', now()->toDateString());

        // Convertir en objets Carbon
        $debut = Carbon::parse($dateDebut)->startOfDay();
        $fin = Carbon::parse($dateFin)->endOfDay();

        // KPIs dynamiques
        $totalControles = Controle::whereBetween('date_controle', [$debut, $fin])->count();
        $controlesConformes = Controle::whereBetween('date_controle', [$debut, $fin])
            ->where('resultat', 'conforme')
            ->count();

        $tauxConformite = $totalControles > 0 ? round(($controlesConformes / $totalControles) * 100, 1) : 0;

        $anomaliesActives = Anomaly::where('status', '!=', 'closed')
            ->where('status', '!=', 'resolved')
            ->whereBetween('created_at', [$debut, $fin])
            ->count();

        $auditsPlanifies = Inspection::where('status', 'scheduled')
            ->whereBetween('started_at', [$debut, $fin])
            ->count();

        // Évolution mensuelle des contrôles
        $evolutionData = $this->getEvolutionData($debut, $fin);

        // Répartition par résultat
        $resultatsData = $this->getResultatsData($debut, $fin);

        // Contrôles à venir (prochains 30 jours)
        $controlesAVenir = Controle::where('prochain_controle', '>=', now()->toDateString())
            ->where('prochain_controle', '<=', now()->addDays(30)->toDateString())
            ->with('vehicle')
            ->orderBy('prochain_controle')
            ->limit(5)
            ->get();

        // Anomalies actives
        $anomaliesActivesListe = Anomaly::whereIn('status', ['open', 'in_progress'])
            ->whereBetween('created_at', [$debut, $fin])
            ->orderBy('severity', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Indicateurs de performance
        $performanceData = $this->getPerformanceData($debut, $fin);

        return view('controle-audit.dashboard', compact(
            'dateDebut',
            'dateFin',
            'totalControles',
            'tauxConformite',
            'anomaliesActives',
            'auditsPlanifies',
            'evolutionData',
            'resultatsData',
            'controlesAVenir',
            'anomaliesActivesListe',
            'performanceData'
        ));
    }

    public function getFilteredData(Request $request)
    {
        $dateDebut = $request->get('date_debut');
        $dateFin = $request->get('date_fin');

        $debut = Carbon::parse($dateDebut)->startOfDay();
        $fin = Carbon::parse($dateFin)->endOfDay();

        // Recalculer les KPIs
        $totalControles = Controle::whereBetween('date_controle', [$debut, $fin])->count();
        $controlesConformes = Controle::whereBetween('date_controle', [$debut, $fin])
            ->where('resultat', 'conforme')
            ->count();

        $tauxConformite = $totalControles > 0 ? round(($controlesConformes / $totalControles) * 100, 1) : 0;

        $anomaliesActives = Anomaly::where('status', '!=', 'closed')
            ->where('status', '!=', 'resolved')
            ->whereBetween('created_at', [$debut, $fin])
            ->count();

        $auditsPlanifies = Inspection::where('status', 'scheduled')
            ->whereBetween('started_at', [$debut, $fin])
            ->count();

        // Évolution des données
        $evolutionData = $this->getEvolutionData($debut, $fin);

        // Répartition par résultat
        $resultatsData = $this->getResultatsData($debut, $fin);

        return response()->json([
            'kpis' => [
                'totalControles' => $totalControles,
                'tauxConformite' => $tauxConformite,
                'anomaliesActives' => $anomaliesActives,
                'auditsPlanifies' => $auditsPlanifies
            ],
            'evolution' => $evolutionData,
            'resultats' => $resultatsData
        ]);
    }

    public function planifs(Request $request)
    {
        $dateDebut = $request->get('date_debut', now()->toDateString());
        $dateFin = $request->get('date_fin', now()->addDays(60)->toDateString());

        $controlesAVenir = Controle::with('vehicle')
            ->whereNotNull('prochain_controle')
            ->whereBetween('prochain_controle', [$dateDebut, $dateFin])
            ->orderBy('prochain_controle')
            ->limit(50)
            ->get();

        $inspectionsPlanifiees = Inspection::with('inspectable')
            ->where('status', 'scheduled')
            ->whereBetween('started_at', [$dateDebut, $dateFin])
            ->orderBy('started_at')
            ->limit(50)
            ->get();

        return view('controle-audit.planifs', compact('controlesAVenir', 'inspectionsPlanifiees', 'dateDebut', 'dateFin'));
    }

    private function getEvolutionData($debut, $fin)
    {
        $labels = [];
        $qualiteData = [];
        $securiteData = [];
        $techniqueData = [];

        $current = $debut->copy();
        while ($current <= $fin) {
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();

            $labels[] = $current->format('M Y');

            $qualiteData[] = Controle::whereBetween('date_controle', [$monthStart, $monthEnd])
                ->where('type_controle', 'qualite')
                ->count();

            $securiteData[] = Controle::whereBetween('date_controle', [$monthStart, $monthEnd])
                ->where('type_controle', 'securite')
                ->count();

            $techniqueData[] = Controle::whereBetween('date_controle', [$monthStart, $monthEnd])
                ->where('type_controle', 'technique')
                ->count();

            $current->addMonth();
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Contrôles Qualité',
                    'data' => $qualiteData,
                    'borderColor' => 'rgb(78, 115, 223)',
                    'backgroundColor' => 'rgba(78, 115, 223, 0.1)',
                    'tension' => 0.4,
                    'fill' => true
                ],
                [
                    'label' => 'Audits Sécurité',
                    'data' => $securiteData,
                    'borderColor' => 'rgb(28, 200, 138)',
                    'backgroundColor' => 'rgba(28, 200, 138, 0.1)',
                    'tension' => 0.4,
                    'fill' => true
                ],
                [
                    'label' => 'Validations Techniques',
                    'data' => $techniqueData,
                    'borderColor' => 'rgb(255, 193, 7)',
                    'backgroundColor' => 'rgba(255, 193, 7, 0.1)',
                    'tension' => 0.4,
                    'fill' => true
                ]
            ]
        ];
    }

    private function getResultatsData($debut, $fin)
    {
        $conforme = Controle::whereBetween('date_controle', [$debut, $fin])
            ->where('resultat', 'conforme')
            ->count();

        $mineur = Controle::whereBetween('date_controle', [$debut, $fin])
            ->where('resultat', 'mineur')
            ->count();

        $majeur = Controle::whereBetween('date_controle', [$debut, $fin])
            ->where('resultat', 'majeur')
            ->count();

        $critique = Controle::whereBetween('date_controle', [$debut, $fin])
            ->where('resultat', 'critique')
            ->count();

        $total = $conforme + $mineur + $majeur + $critique;

        return [
            'labels' => ['Conforme', 'Mineur', 'Majeur', 'Critique'],
            'datasets' => [[
                'data' => [$conforme, $mineur, $majeur, $critique],
                'backgroundColor' => [
                    'rgb(28, 200, 138)',
                    'rgb(255, 193, 7)',
                    'rgb(255, 152, 0)',
                    'rgb(220, 53, 69)'
                ],
                'borderWidth' => 2
            ]]
        ];
    }

    private function getPerformanceData($debut, $fin)
    {
        // Calculer les métriques de performance pour la période
        $totalControles = Controle::whereBetween('date_controle', [$debut, $fin])->count();

        // Contrôles dans les délais (simplifié - considérer tous comme dans les délais pour l'exemple)
        $controlesDelais = $totalControles;

        // Résolution d'anomalies
        $anomaliesResolues = Anomaly::whereBetween('updated_at', [$debut, $fin])
            ->where('status', 'resolved')
            ->count();

        $totalAnomalies = Anomaly::whereBetween('created_at', [$debut, $fin])->count();
        $tauxResolution = $totalAnomalies > 0 ? round(($anomaliesResolues / $totalAnomalies) * 100) : 0;

        // Moyenne interventions/jour
        $joursPeriode = $debut->diffInDays($fin) + 1;
        $moyenneInterventions = $joursPeriode > 0 ? round($totalControles / $joursPeriode, 1) : 0;

        // Délai moyen résolution (simplifié - 12 jours pour l'exemple)
        $delaiMoyenResolution = 12;

        return [
            'controlesDelais' => $controlesDelais,
            'totalControles' => $totalControles,
            'tauxResolution' => $tauxResolution,
            'moyenneInterventions' => $moyenneInterventions,
            'delaiMoyenResolution' => $delaiMoyenResolution
        ];
    }
}
