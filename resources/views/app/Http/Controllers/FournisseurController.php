<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use App\Models\CategorieFournisseur;
use App\Models\ContratFournisseur;
use App\Models\CommandeFournisseur;
use App\Models\EvaluationFournisseur;
use App\Models\DocumentFournisseur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Exports\FournisseursExport;
use App\Imports\FournisseursImport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FournisseurController extends Controller
{
    /**
     * Afficher le tableau de bord des fournisseurs
     */
    public function dashboard()
    {
        $stats = [
            'total' => Fournisseur::count(),
            'actifs' => Fournisseur::where('est_actif', true)->count(),
            'inactifs' => Fournisseur::where('est_actif', false)->count(),
            'sans_contrat' => Fournisseur::doesntHave('contrats')->count(),
            'contrats_actifs' => ContratFournisseur::where('statut', 'en_cours')->count(),
            'contrats_expirant' => ContratFournisseur::where('date_fin', '<=', now()->addDays(30))
                ->where('date_fin', '>=', now())
                ->where('statut', 'en_cours')
                ->count(),
            'commandes_attente' => CommandeFournisseur::where('statut', 'en_attente')->count(),
            'commandes_livrees' => CommandeFournisseur::where('statut', 'livree')->count(),
            'evaluations_attente' => EvaluationFournisseur::where('statut', 'en_cours')->count(),
            'documents_expirant' => DocumentFournisseur::where('date_expiration', '<=', now()->addDays(30))
                ->where('date_expiration', '>=', now())
                ->where('est_obligatoire', true)
                ->count(),
        ];

        // Derniers fournisseurs ajoutés
        $derniersFournisseurs = Fournisseur::with('categorie')
            ->latest()
            ->take(5)
            ->get();

        // Contrats arrivant à échéance
        $contratsExpirant = ContratFournisseur::with('fournisseur')
            ->where('date_fin', '<=', now()->addDays(30))
            ->where('date_fin', '>=', now())
            ->where('statut', 'en_cours')
            ->orderBy('date_fin')
            ->take(5)
            ->get();

        // Commandes en retard
        $commandesRetard = CommandeFournisseur::with('fournisseur')
            ->where('date_livraison_prevue', '<', now())
            ->whereIn('statut', ['en_attente', 'validee', 'en_cours'])
            ->orderBy('date_livraison_prevue')
            ->take(5)
            ->get();

        // Documents expirant
        $documentsExpirant = DocumentFournisseur::with('fournisseur')
            ->where('date_expiration', '<=', now()->addDays(30))
            ->where('date_expiration', '>=', now())
            ->where('est_obligatoire', true)
            ->orderBy('date_expiration')
            ->take(5)
            ->get();

        return view('fournisseurs.dashboard', compact(
            'stats',
            'derniersFournisseurs',
            'contratsExpirant',
            'commandesRetard',
            'documentsExpirant'
        ));
    }

    /**
     * Afficher la liste des fournisseurs
     */
    public function index(Request $request)
    {
        try {
            $query = Fournisseur::with('categorie')
                ->withCount(['contrats', 'commandes', 'evaluations']);

            // Filtres
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function($q) use ($search) {
                    $q->where('raison_sociale', 'like', "%{$search}%")
                      ->orWhere('siret', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('telephone', 'like', "%{$search}%");
                });
            }

            if ($request->filled('categorie_id')) {
                $query->where('categorie_id', $request->input('categorie_id'));
            }

            if ($request->filled('statut')) {
                $query->where('est_actif', $request->input('statut') === 'actif');
            }

            if ($request->filled('ville')) {
                $query->where('ville', 'like', "%{$request->input('ville')}%");
            }

            // Tri
            $sortField = $request->input('sort_field', 'raison_sociale');
            $sortDirection = $request->input('sort_direction', 'asc');

            if (in_array($sortField, ['raison_sociale', 'ville', 'created_at'])) {
                $query->orderBy($sortField, $sortDirection);
            } elseif ($sortField === 'categorie') {
                $query->join('categorie_fournisseurs', 'fournisseurs.categorie_id', '=', 'categorie_fournisseurs.id')
                    ->orderBy('categorie_fournisseurs.nom', $sortDirection);
            }

            $fournisseurs = $query->paginate(15)->withQueryString();
        } catch (\Exception $e) {
            $fournisseurs = collect([]);
        }

        try {
            $categories = CategorieFournisseur::orderBy('nom')->get();
        } catch (\Exception $e) {
            $categories = collect([]);
        }

        // Synthèse pour les cartes de résumé
        try {
            $suppliersTotal = Fournisseur::count();
            $suppliersActive = Fournisseur::where('est_actif', true)->count();
            $categoriesCount = CategorieFournisseur::count();
            $lastCategoryAdded = optional(CategorieFournisseur::latest()->first())->nom;
        } catch (\Exception $e) {
            $suppliersTotal = 0;
            $suppliersActive = 0;
            $categoriesCount = 0;
            $lastCategoryAdded = null;
        }

        return view(
            'fournisseurs.index',
            compact(
                'fournisseurs',
                'categories',
                'suppliersTotal',
                'suppliersActive',
                'categoriesCount',
                'lastCategoryAdded'
            )
        );
    }

    public function listSimple()
    {
        try {
            $fournisseurs = collect([]);

            // Vérifier si la table fournisseurs existe
            if (Schema::hasTable('fournisseurs')) {
                $query = Fournisseur::query();

                // Essayer de charger la relation categorie
                try {
                    $query->with('categorie');
                } catch (\Throwable $e) {
                    // Continuer sans la relation categorie
                }

                try {
                    $fournisseurs = $query->orderBy('raison_sociale')->get();
                } catch (\Exception $e) {
                    // Si la requête échoue, essayer une requête plus simple
                    try {
                        $fournisseurs = DB::table('fournisseurs')->orderBy('raison_sociale')->get();
                    } catch (\Exception $e2) {
                        $fournisseurs = collect([]);
                    }
                }
            }
        } catch (\Exception $e) {
            $fournisseurs = collect([]);
        }

        return view('fournisseurs.list', compact('fournisseurs'));
    }

    /**
     * Afficher le formulaire de création d'un fournisseur
     */
    public function create()
    {
        $categories = CategorieFournisseur::orderBy('nom')->get();
        $pays = $this->getPaysList();

        return view('fournisseurs.create', compact('categories', 'pays'));
    }

    /**
     * Enregistrer un nouveau fournisseur
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'raison_sociale' => 'required|string|max:255',
            'forme_juridique' => 'nullable|string|max:100',
            'siret' => 'nullable|string|max:20|unique:fournisseurs,siret',
            'tva_intracom' => 'nullable|string|max:20',
            'categorie_id' => 'nullable|exists:categorie_fournisseurs,id',
            'adresse' => 'required|string|max:255',
            'code_postal' => 'required|string|max:10',
            'ville' => 'required|string|max:100',
            'pays' => 'required|string|max:100',
            'telephone' => 'required|string|max:20',
            'email' => 'required|email|max:100',
            'site_web' => 'nullable|url|max:255',
            'contacts' => 'nullable|array',
            'contacts.*.nom' => 'required_with:contacts|string|max:100',
            'contacts.*.prenom' => 'nullable|string|max:100',
            'contacts.*.fonction' => 'nullable|string|max:100',
            'contacts.*.email' => 'nullable|email|max:100',
            'contacts.*.telephone' => 'nullable|string|max:20',
            'contacts.*.portable' => 'nullable|string|max:20',
            'contacts.*.principal' => 'nullable|boolean',
            'logo' => 'nullable|image|max:2048',
            'est_actif' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        // Gestion du logo
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos-fournisseurs', 'public');
            $validated['logo_path'] = $path;
        }

        // Création du fournisseur
        $fournisseur = Fournisseur::create($validated);

        return redirect()
            ->route('fournisseurs.show', $fournisseur)
            ->with('success', 'Le fournisseur a été créé avec succès.');
    }

    /**
     * Afficher les détails d'un fournisseur
     */
    public function show(Fournisseur $fournisseur)
    {
        $fournisseur->load([
            'categorie',
            'contrats' => function($query) {
                $query->orderBy('date_fin', 'desc');
            },
            'commandes' => function($query) {
                $query->orderBy('date_commande', 'desc')->take(5);
            },
            'evaluations' => function($query) {
                $query->orderBy('date_evaluation', 'desc')->take(3);
            },
            'documents' => function($query) {
                $query->orderBy('date_expiration', 'asc')
                    ->where('date_expiration', '>=', now())
                    ->orWhereNull('date_expiration')
                    ->take(5);
            }
        ]);

        // Statistiques
        $stats = [
            'commandes' => [
                'total' => $fournisseur->commandes()->count(),
                'en_attente' => $fournisseur->commandes()->where('statut', 'en_attente')->count(),
                'en_cours' => $fournisseur->commandes()->where('statut', 'en_cours')->count(),
                'livrees' => $fournisseur->commandes()->where('statut', 'livree')->count(),
            ],
            'contrats' => [
                'actifs' => $fournisseur->contrats()->where('statut', 'en_cours')->count(),
                'expirant' => $fournisseur->contrats()
                    ->where('date_fin', '<=', now()->addDays(30))
                    ->where('date_fin', '>=', now())
                    ->where('statut', 'en_cours')
                    ->count(),
            ],
            'documents' => [
                'expirant' => $fournisseur->documents()
                    ->where('date_expiration', '<=', now()->addDays(30))
                    ->where('date_expiration', '>=', now())
                    ->where('est_obligatoire', true)
                    ->count(),
                'expires' => $fournisseur->documents()
                    ->where('date_expiration', '<', now())
                    ->where('est_obligatoire', true)
                    ->count(),
            ]
        ];

        return view('fournisseurs.show', compact('fournisseur', 'stats'));
    }

    /**
     * Afficher le formulaire de modification d'un fournisseur
     */
    public function edit(Fournisseur $fournisseur)
    {
        $categories = CategorieFournisseur::orderBy('nom')->get();
        $pays = $this->getPaysList();

        return view('fournisseurs.edit', compact('fournisseur', 'categories', 'pays'));
    }

    /**
     * Mettre à jour un fournisseur
     */
    public function update(Request $request, Fournisseur $fournisseur)
    {
        $validated = $request->validate([
            'raison_sociale' => 'required|string|max:255',
            'forme_juridique' => 'nullable|string|max:100',
            'siret' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('fournisseurs', 'siret')->ignore($fournisseur->id)
            ],
            'tva_intracom' => 'nullable|string|max:20',
            'categorie_id' => 'nullable|exists:categorie_fournisseurs,id',
            'adresse' => 'required|string|max:255',
            'code_postal' => 'required|string|max:10',
            'ville' => 'required|string|max:100',
            'pays' => 'required|string|max:100',
            'telephone' => 'required|string|max:20',
            'email' => 'required|email|max:100',
            'site_web' => 'nullable|url|max:255',
            'contacts' => 'nullable|array',
            'contacts.*.nom' => 'required_with:contacts|string|max:100',
            'contacts.*.prenom' => 'nullable|string|max:100',
            'contacts.*.fonction' => 'nullable|string|max:100',
            'contacts.*.email' => 'nullable|email|max:100',
            'contacts.*.telephone' => 'nullable|string|max:20',
            'contacts.*.portable' => 'nullable|string|max:20',
            'contacts.*.principal' => 'nullable|boolean',
            'logo' => 'nullable|image|max:2048',
            'supprimer_logo' => 'nullable|boolean',
            'est_actif' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        // Gestion du logo
        if ($request->boolean('supprimer_logo') && $fournisseur->logo_path) {
            Storage::disk('public')->delete($fournisseur->logo_path);
            $validated['logo_path'] = null;
        } elseif ($request->hasFile('logo')) {
            // Supprimer l'ancien logo si existe
            if ($fournisseur->logo_path) {
                Storage::disk('public')->delete($fournisseur->logo_path);
            }

            $path = $request->file('logo')->store('logos-fournisseurs', 'public');
            $validated['logo_path'] = $path;
        }

        $fournisseur->update($validated);

        return redirect()
            ->route('fournisseurs.show', $fournisseur)
            ->with('success', 'Les informations du fournisseur ont été mises à jour avec succès.');
    }

    /**
     * Supprimer un fournisseur
     */
    public function destroy(Fournisseur $fournisseur)
    {
        // Vérifier s'il y a des relations avant de supprimer
        $hasRelations = $fournisseur->contrats()->exists() ||
                        $fournisseur->commandes()->exists() ||
                        $fournisseur->evaluations()->exists() ||
                        $fournisseur->documents()->exists();

        if ($hasRelations) {
            return back()
                ->with('error', 'Impossible de supprimer ce fournisseur car il est associé à des contrats, commandes, évaluations ou documents.');
        }

        // Supprimer le logo si existe
        if ($fournisseur->logo_path) {
            Storage::disk('public')->delete($fournisseur->logo_path);
        }

        $fournisseur->delete();

        return redirect()
            ->route('fournisseurs.index')
            ->with('success', 'Le fournisseur a été supprimé avec succès.');
    }

    /**
     * Exporter les fournisseurs au format Excel
     */
    public function export()
    {
        return Excel::download(new FournisseursExport, 'fournisseurs-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Afficher le formulaire d'import de fournisseurs
     */
    public function importForm()
    {
        return view('fournisseurs.import');
    }

    /**
     * Importer des fournisseurs depuis un fichier Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'fichier' => 'required|file|mimes:xlsx,xls,csv',
            'mode_import' => 'required|in:creer,maj,creer_maj',
        ]);

        $import = new FournisseursImport($request->input('mode_import'));

        try {
            Excel::import($import, $request->file('fichier'));

            $message = sprintf(
                'Import terminé avec succès. %d enregistrements traités, %d créés, %d mis à jour, %d ignorés.',
                $import->getRowCount(),
                $import->getCreatedCount(),
                $import->getUpdatedCount(),
                $import->getSkippedCount()
            );

            if (!empty($import->getErrors())) {
                return back()
                    ->with('warning', $message)
                    ->with('import_errors', $import->getErrors());
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Une erreur est survenue lors de l\'import : ' . $e->getMessage());
        }
    }

    /**
     * Télécharger un modèle d'import
     */
    public function downloadTemplate()
    {
        $filePath = storage_path('app/templates/import-fournisseurs-template.xlsx');

        if (!file_exists($filePath)) {
            // Générer un modèle vide si le fichier n'existe pas
            return Excel::download(new FournisseursExport([]), 'import-fournisseurs-template.xlsx');
        }

        return response()->download($filePath, 'import-fournisseurs-template.xlsx');
    }

    /**
     * Générer un PDF avec les informations du fournisseur
     */
    public function pdf(Fournisseur $fournisseur)
    {
        $fournisseur->load([
            'categorie',
            'contrats' => function($query) {
                $query->orderBy('date_fin', 'desc');
            },
            'commandes' => function($query) {
                $query->orderBy('date_commande', 'desc')->take(5);
            },
            'evaluations' => function($query) {
                $query->orderBy('date_evaluation', 'desc')->take(3);
            },
            'documents' => function($query) {
                $query->orderBy('date_expiration', 'asc')->take(5);
            }
        ]);

        $pdf = PDF::loadView('fournisseurs.pdf', compact('fournisseur'));

        return $pdf->download('fournisseur-' . Str::slug($fournisseur->raison_sociale) . '.pdf');
    }

    /**
     * Obtenir la liste des pays pour les sélecteurs
     */
    private function getPaysList()
    {
        return [
            'France' => 'France',
            'Belgique' => 'Belgique',
            'Suisse' => 'Suisse',
            'Luxembourg' => 'Luxembourg',
            'Allemagne' => 'Allemagne',
            'Espagne' => 'Espagne',
            'Italie' => 'Italie',
            'Royaume-Uni' => 'Royaume-Uni',
            'Pays-Bas' => 'Pays-Bas',
            'Autre' => 'Autre',
        ];
    }
}
