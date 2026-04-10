<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PersonnelPaieController extends Controller
{
    /**
     * Afficher l'historique des paies d'un personnel
     */
    public function index(Request $request, Personnel $personnel)
    {
        $this->authorize('view', $personnel);

        // Récupérer les paies avec filtres
        $query = $personnel->paies();

        if ($request->filled('mois')) {
            $query->whereMonth('periode', $request->mois);
        }

        if ($request->filled('annee')) {
            $query->whereYear('periode', $request->annee);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $paies = $query->orderBy('periode', 'desc')->get();

        // Calcul des statistiques
        $totalPaiesAnnee = $personnel->paies()
            ->whereYear('periode', now()->year)
            ->where('statut', 'PAYE')
            ->sum('salaire_net');

        return view('rh.personnel.paies', compact('personnel', 'paies', 'totalPaiesAnnee'));
    }

    /**
     * Enregistrer une nouvelle fiche de paie
     */
    public function store(Request $request, Personnel $personnel)
    {
        $this->authorize('update', $personnel);

        $validated = $request->validate([
            'periode' => 'required|date|before_or_equal:today',
            'salaire_base' => 'required|numeric|min:0',
            'primes' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'salaire_net' => 'nullable|numeric|min:0',
            'statut' => 'required|in:EN_ATTENTE,VALIDE,PAYE',
            'date_paiement' => 'nullable|date',
            'observations' => 'nullable|string|max:1000',
        ]);

        // Vérifier si une paie existe déjà pour cette période
        $periode = \Carbon\Carbon::parse($validated['periode'])->format('Y-m-01');
        $existingPaie = $personnel->paies()->where('periode', $periode)->first();

        if ($existingPaie) {
            return back()->with('error', 'Une fiche de paie existe déjà pour cette période');
        }

        // Calcul du salaire net si non fourni
        if (!isset($validated['salaire_net'])) {
            $validated['salaire_net'] = $validated['salaire_base'] + 
                                      ($validated['primes'] ?? 0) - 
                                      ($validated['deductions'] ?? 0);
        }

        // Création de la fiche de paie
        $personnel->paies()->create([
            'periode' => $periode,
            'salaire_base' => $validated['salaire_base'],
            'primes' => $validated['primes'] ?? 0,
            'deductions' => $validated['deductions'] ?? 0,
            'salaire_net' => $validated['salaire_net'],
            'statut' => $validated['statut'],
            'date_paiement' => $validated['date_paiement'],
            'observations' => $validated['observations'],
            'cree_par' => Auth::id(),
        ]);

        return back()->with('success', 'Fiche de paie enregistrée avec succès');
    }

    /**
     * Marquer une paie comme payée
     */
    public function marquerPaye(Personnel $personnel, $paieId)
    {
        $this->authorize('update', $personnel);

        $paie = $personnel->paies()->findOrFail($paieId);

        if ($paie->statut !== 'VALIDE') {
            return back()->with('error', 'Seules les fiches validées peuvent être marquées comme payées');
        }

        $paie->update([
            'statut' => 'PAYE',
            'date_paiement' => now(),
            'paye_par' => Auth::id(),
        ]);

        return back()->with('success', 'Fiche de paie marquée comme payée avec succès');
    }

    /**
     * Générer le bulletin de paie en PDF
     */
    public function bulletinPdf(Personnel $personnel, $paieId)
    {
        $this->authorize('view', $personnel);

        $paie = $personnel->paies()->findOrFail($paieId);

        $pdf = PDF::loadView('rh.personnel.bulletin-pdf', compact('personnel', 'paie'));
        
        $filename = 'bulletin_paie_' . $personnel->matricule . '_' . 
                   \Carbon\Carbon::parse($paie->periode)->format('m_Y') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Exporter l'historique des paies
     */
    public function export(Request $request, Personnel $personnel)
    {
        $this->authorize('view', $personnel);

        $paies = $personnel->paies()
            ->when($request->filled('annee'), function($query) use ($request) {
                return $query->whereYear('periode', $request->annee);
            })
            ->orderBy('periode', 'desc')
            ->get();

        $filename = 'export_paies_' . $personnel->matricule . '_' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($paies) {
            $file = fopen('php://output', 'w');
            
            // En-tête CSV
            fputcsv($file, [
                'Période', 'Salaire Base', 'Primes', 'Déductions', 
                'Salaire Net', 'Statut', 'Date Paiement', 'Observations'
            ]);

            // Données
            foreach ($paies as $paie) {
                fputcsv($file, [
                    \Carbon\Carbon::parse($paie->periode)->format('m/Y'),
                    number_format($paie->salaire_base, 0, ',', ' '),
                    number_format($paie->primes, 0, ',', ' '),
                    number_format($paie->deductions, 0, ',', ' '),
                    number_format($paie->salaire_net, 0, ',', ' '),
                    $paie->statut,
                    $paie->date_paiement ? \Carbon\Carbon::parse($paie->date_paiement)->format('d/m/Y') : '',
                    $paie->observations ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Supprimer une fiche de paie
     */
    public function destroy(Personnel $personnel, $paieId)
    {
        $this->authorize('update', $personnel);

        $paie = $personnel->paies()->findOrFail($paieId);

        if ($paie->statut === 'PAYE') {
            return back()->with('error', 'Impossible de supprimer une fiche de paie déjà payée');
        }

        $paie->delete();

        return back()->with('success', 'Fiche de paie supprimée avec succès');
    }
}
