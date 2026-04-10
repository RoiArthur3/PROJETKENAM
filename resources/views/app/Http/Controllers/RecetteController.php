<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecetteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Récupérer les paramètres de filtrage
            $dateDebut = request('date_debut');
            $dateFin = request('date_fin');
            $statut = request('statut');

            // Construire la requête avec les filtres
            $query = DB::table('recettes')
                ->orderBy('date_recette', 'desc');

            if ($dateDebut) {
                $query->whereDate('date_recette', '>=', $dateDebut);
            }

            if ($dateFin) {
                $query->whereDate('date_recette', '<=', $dateFin);
            }

            if ($statut) {
                $query->where('statut', $statut);
            }

            $recettes = $query->paginate(15);

            // Calculer les statistiques
            $totalRecettes = $this->safeSum('recettes', 'montant');
            $recettesMois = $this->safeSumWhere('recettes', 'montant', 'date_recette', now()->month, now()->year);
            $recettesEnAttente = $this->safeCountWhere('recettes', 'statut', 'en_attente');
            $moyenneMensuelle = $this->calculateMoyenneMensuelle();

            return view('comptabilite.recettes.index', compact('recettes', 'totalRecettes', 'recettesMois', 'recettesEnAttente', 'moyenneMensuelle'));
        } catch (\Exception $e) {
            Log::error('Erreur dans RecetteController@index: ' . $e->getMessage());
            
            // Retourner des valeurs par défaut en cas d'erreur
            return view('comptabilite.recettes.index', [
                'recettes' => collect([]),
                'totalRecettes' => 0,
                'recettesMois' => 0,
                'recettesEnAttente' => 0,
                'moyenneMensuelle' => 0
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('comptabilite.recettes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'client' => 'required|string|max:255',
                'description' => 'nullable|string',
                'montant' => 'required|numeric|min:0',
                'date_recette' => 'required|date',
                'mode_paiement' => 'nullable|string|max:100',
                'reference' => 'nullable|string|max:50',
            ]);

            // Générer une référence si non fournie
            if (empty($validated['reference'])) {
                $validated['reference'] = 'REC-' . date('Ymd') . '-' . str_pad(DB::table('recettes')->count() + 1, 3, '0', STR_PAD_LEFT);
            }

            $validated['statut'] = 'en_attente';
            $validated['created_at'] = now();
            $validated['updated_at'] = now();

            DB::table('recettes')->insert($validated);

            return redirect()->route('recettes.index')->with('success', 'Recette créée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur dans RecetteController@store: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la création de la recette.');
        }
    }

    /**
     * Valider une recette
     */
    public function valider($id)
    {
        try {
            DB::table('recettes')
                ->where('id', $id)
                ->update([
                    'statut' => 'validee',
                    'updated_at' => now()
                ]);

            return redirect()->route('recettes.index')->with('success', 'Recette validée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur dans RecetteController@valider: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la validation de la recette.');
        }
    }

    /**
     * Annuler une recette
     */
    public function annuler($id)
    {
        try {
            DB::table('recettes')
                ->where('id', $id)
                ->update([
                    'statut' => 'annulee',
                    'updated_at' => now()
                ]);

            return redirect()->route('recettes.index')->with('success', 'Recette annulée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur dans RecetteController@annuler: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de l\'annulation de la recette.');
        }
    }

    /**
     * Exporter les recettes en CSV
     */
    public function export()
    {
        try {
            $recettes = DB::table('recettes')
                ->orderBy('date_recette', 'desc')
                ->get();

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="recettes_' . date('Y-m-d') . '.csv"',
            ];

            $callback = function() use ($recettes) {
                $file = fopen('php://output', 'w');
                
                // En-tête CSV
                fputcsv($file, ['Référence', 'Date', 'Client', 'Description', 'Montant', 'Statut'], ';');

                foreach ($recettes as $recette) {
                    fputcsv($file, [
                        $recette->reference ?? '',
                        $recette->date_recette ?? '',
                        $recette->client ?? '',
                        $recette->description ?? '',
                        $recette->montant ?? 0,
                        $recette->statut ?? ''
                    ], ';');
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error('Erreur dans RecetteController@export: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de l\'exportation des recettes.');
        }
    }

    // Méthodes utilitaires
    private function safeSum($table, $column)
    {
        try {
            return DB::table($table)->sum($column) ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function safeSumWhere($table, $column, $dateColumn, $month, $year)
    {
        try {
            return DB::table($table)
                ->whereMonth($dateColumn, $month)
                ->whereYear($dateColumn, $year)
                ->sum($column) ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function safeCountWhere($table, $column, $value)
    {
        try {
            return DB::table($table)->where($column, $value)->count() ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function calculateMoyenneMensuelle()
    {
        try {
            $total = $this->safeSum('recettes', 'montant');
            $months = DB::table('recettes')
                ->selectRaw('COUNT(DISTINCT DATE_FORMAT(date_recette, "%Y-%m")) as months')
                ->first()->months ?? 1;
            
            return $months > 0 ? $total / $months : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
}
