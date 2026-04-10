<?php

namespace App\Http\Controllers;

use App\Models\Controle;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ControleurController extends Controller
{
    /**
     * Affiche la liste des contrôles
     */
    public function index(Request $request)
    {
        $query = Controle::with(['vehicle', 'user'])
            ->orderBy('date_controle', 'desc');

        // Filtres
        if ($request->filled('vehicule_id')) {
            $query->where('vehicule_id', $request->vehicule_id);
        }

        if ($request->filled('type_controle')) {
            $query->where('type_controle', $request->type_controle);
        }

        if ($request->filled('resultat')) {
            $query->where('resultat', $request->resultat);
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('date_controle', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('date_controle', '<=', $request->date_fin);
        }

        $controles = $query->paginate(15)->withQueryString();
        $vehicules = Vehicle::orderBy('immatriculation')->get();

        // Statistiques
        $totalControles = Controle::count();
        $controlesConformes = Controle::where('resultat', 'conforme')->count();
        $controlesNonConformes = Controle::where('resultat', 'non_conforme')->count();
        $controlesEnAttente = Controle::where('resultat', 'en_attente')->count();

        return view('controleur.index', [
            'controles' => $controles,
            'vehicules' => $vehicules,
            'title' => 'Gestion des Contrôles',
            'active_menu' => 'controleur',
            'totalControles' => $totalControles,
            'controlesConformes' => $controlesConformes,
            'controlesNonConformes' => $controlesNonConformes,
            'controlesEnAttente' => $controlesEnAttente
        ]);
    }

    /**
     * Affiche le formulaire de création d'un contrôle
     */
    public function create()
    {
        $vehicules = Vehicle::orderBy('immatriculation')->get();
        
        return view('controleur.create', [
            'vehicules' => $vehicules,
            'title' => 'Nouveau Contrôle',
            'active_menu' => 'controleur'
        ]);
    }

    /**
     * Enregistre un nouveau contrôle
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicule_id' => 'required|exists:vehicles,id',
            'date_controle' => 'required|date',
            'type_controle' => 'required|string|max:100',
            'resultat' => 'required|in:conforme,non_conforme,en_attente',
            'commentaires' => 'nullable|string',
            'kilometrage' => 'nullable|integer|min:0',
            'prochain_controle' => 'nullable|date|after_or_equal:date_controle',
        ]);

        try {
            DB::beginTransaction();
            
            // Mise à jour du kilométrage du véhicule si fourni
            if (isset($validated['kilometrage'])) {
                $vehicule = Vehicle::findOrFail($validated['vehicule_id']);
                if ($vehicule && $validated['kilometrage'] > $vehicule->kilometrage) {
                    $vehicule->update(['kilometrage' => $validated['kilometrage']]);
                }
            }
            
            // Création du contrôle
            $controle = Controle::create([
                'vehicule_id' => $validated['vehicule_id'],
                'date_controle' => $validated['date_controle'],
                'type_controle' => $validated['type_controle'],
                'resultat' => $validated['resultat'],
                'commentaires' => $validated['commentaires'] ?? null,
                'kilometrage' => $validated['kilometrage'] ?? null,
                'prochain_controle' => $validated['prochain_controle'] ?? null,
                'user_id' => auth()->id(), // Sera null si l'utilisateur n'est pas connecté
                'statut_soumission' => 'brouillon',
            ]);
            
            DB::commit();

            $message = 'Contrôle enregistré avec succès. ';
            $message .= $request->has('submit_to_services') 
                ? 'Le contrôle a été soumis aux services sélectionnés.'
                : 'Vous pouvez maintenant le soumettre à des services si nécessaire.';

            return redirect()
                ->route('controleur.show', $controle->id)
                ->with('success', $message);
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Une erreur est survenue lors de l\'enregistrement du contrôle: ' . $e->getMessage());
        }
    }

    /**
     * Soumet un contrôle à des services spécifiques
     */
    public function submitToServices(Request $request, Controle $controle)
    {
        $validated = $request->validate([
            'service_ids' => 'required|array',
            'service_ids.*' => 'exists:services,id',
            'commentaire_soumission' => 'nullable|string',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Mettre à jour le statut de soumission
            $controle->update([
                'statut_soumission' => 'soumis',
                'commentaire_soumission' => $validated['commentaire_soumission'] ?? null,
                'soumis_le' => now(),
            ]);
            
            // Attacher les services sélectionnés
            $servicesToAttach = [];
            foreach ($validated['service_ids'] as $serviceId) {
                $servicesToAttach[$serviceId] = [
                    'statut' => 'en_attente',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            $controle->services()->syncWithoutDetaching($servicesToAttach);
            
            DB::commit();
            
            // Ici, vous pourriez ajouter l'envoi d'emails aux services
            // $this->notifyServices($controle, $validated['service_ids']);
            
            return redirect()
                ->route('controleur.show', $controle->id)
                ->with('success', 'Le contrôle a été soumis avec succès aux services sélectionnés.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de la soumission du contrôle: ' . $e->getMessage());
        }
    }

    /**
     * Affiche les détails d'un contrôle
     */
    public function show(Controle $controle)
    {
        $controle->load([
            'vehicle', 
            'user',
            'vehicle.controles' => function($query) use ($controle) {
                $query->where('id', '!=', $controle->id)
                    ->orderBy('date_controle', 'desc')
                    ->limit(5);
            },
            'vehicle.controles.user'
        ]);
        
        // Récupérer les statistiques pour le tableau de bord
        $stats = [
            'total_controles' => Controle::where('vehicule_id', $controle->vehicule_id)->count(),
            'dernier_controle' => Controle::where('vehicule_id', $controle->vehicule_id)
                ->where('id', '!=', $controle->id)
                ->orderBy('date_controle', 'desc')
                ->first(),
            'prochain_controle' => $controle->prochain_controle,
        ];
        
        return view('controleur.show', [
            'controle' => $controle,
            'title' => 'Détails du Contrôle #' . str_pad($controle->id, 6, '0', STR_PAD_LEFT),
            'active_menu' => 'controleur',
            'stats' => $stats,
        ]);
    }

    /**
     * Affiche le formulaire de modification d'un contrôle
     */
    public function edit(Controle $controle)
    {
        // Si l'utilisateur n'est pas connecté, on le redirige vers la page de visualisation
        if (!auth()->check()) {
            return redirect()->route('controleur.show', $controle->id);
        }
        
        $vehicules = Vehicle::orderBy('immatriculation')->get();
        
        // Vérifier si l'utilisateur a la permission de modifier ce contrôle
        if (auth()->user()->role !== 'admin' && $controle->user_id !== auth()->id()) {
            return redirect()->route('controleur.show', $controle->id)
                ->with('warning', 'Vous n\'êtes pas autorisé à modifier ce contrôle.');
        }
        
        // Préparer les données pour les champs de formulaire
        $controle->load(['vehicle.controles' => function($query) use ($controle) {
            $query->where('id', '!=', $controle->id)
                ->orderBy('date_controle', 'desc')
                ->limit(5);
        }]);
        
        return view('controleur.edit', [
            'controle' => $controle,
            'vehicules' => $vehicules,
            'title' => 'Modifier le Contrôle #' . str_pad($controle->id, 6, '0', STR_PAD_LEFT),
            'active_menu' => 'controleur',
            'historique' => $controle->vehicle->controles ?? collect()
        ]);
    }

    /**
     * Met à jour un contrôle existant
     */
    public function update(Request $request, Controle $controle)
    {
        // Vérifier si l'utilisateur est connecté
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Vous devez être connecté pour effectuer cette action.');
        }
        
        $validated = $request->validate([
            'vehicule_id' => 'required|exists:vehicles,id',
            'date_controle' => 'required|date',
            'type_controle' => 'required|string|max:100',
            'resultat' => 'required|in:conforme,non_conforme,en_attente',
            'commentaire' => 'nullable|string',
            'kilometrage' => 'nullable|integer|min:0',
            'prochain_controle' => 'nullable|date|after_or_equal:date_controle',
        ]);

        try {
            DB::beginTransaction();
            
            // Vérifier si l'utilisateur a la permission de modifier ce contrôle
            if (auth()->user()->role !== 'admin' && $controle->user_id !== auth()->id()) {
                return redirect()->route('controleur.show', $controle->id)
                    ->with('error', 'Vous n\'êtes pas autorisé à modifier ce contrôle.');
            }
            
            // Mise à jour du kilométrage du véhicule si fourni
            if (isset($validated['kilometrage'])) {
                $vehicule = $controle->vehicule;
                if ($vehicule && $validated['kilometrage'] > $vehicule->kilometrage) {
                    $vehicule->update(['kilometrage' => $validated['kilometrage']]);
                }
            }
            
            // Mise à jour du contrôle
            $controle->update([
                'vehicule_id' => $validated['vehicule_id'],
                'date_controle' => $validated['date_controle'],
                'type_controle' => $validated['type_controle'],
                'resultat' => $validated['resultat'],
                'commentaire' => $validated['commentaire'] ?? null,
                'kilometrage' => $validated['kilometrage'] ?? null,
                'prochain_controle' => $validated['prochain_controle'] ?? null,
                'user_id' => auth()->id(), // Mettre à jour l'utilisateur qui a effectué la modification
            ]);
            
            DB::commit();

            return redirect()
                ->route('controleur.show', $controle->id)
                ->with('success', 'Contrôle mis à jour avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }

    /**
     * Supprime un contrôle
     */
    public function destroy(Controle $controle)
    {
        try {
            DB::beginTransaction();
            
            // Vérifier si l'utilisateur a la permission de supprimer ce contrôle
            if (auth()->user()->role !== 'admin' && $controle->user_id !== auth()->id()) {
                abort(403, 'Vous n\'êtes pas autorisé à supprimer ce contrôle.');
            }
            
            // Vérifier s'il existe des dépendances avant de supprimer
            $dependances = [];
            
            // Exemple de vérification de dépendances (à adapter selon votre modèle)
            // if ($controle->documents()->count() > 0) {
            //     $dependances[] = 'documents';
            // }
            
            if (!empty($dependances)) {
                return back()
                    ->with('error', 'Impossible de supprimer ce contrôle car il est lié à d\'autres éléments : ' . implode(', ', $dependances));
            }
            
            $controle->delete();
            
            DB::commit();

            return redirect()
                ->route('controleur.index')
                ->with('success', 'Contrôle supprimé avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }
    
    /**
     * Exporte la liste des contrôles au format Excel
     */
    public function export(Request $request)
    {
        $query = Controle::with(['vehicle', 'user'])
            ->orderBy('date_controle', 'desc');

        // Appliquer les mêmes filtres que pour l'index
        if ($request->filled('vehicule_id')) {
            $query->where('vehicule_id', $request->vehicule_id);
        }

        if ($request->filled('type_controle')) {
            $query->where('type_controle', 'like', '%' . $request->type_controle . '%');
        }

        if ($request->filled('resultat')) {
            $query->where('resultat', $request->resultat);
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('date_controle', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('date_controle', '<=', $request->date_fin);
        }

        $controles = $query->get();

        $fileName = 'controles-export-' . now()->format('Y-m-d-H-i-s') . '.xlsx';
        
        // Créer un fichier Excel temporaire
        $file = tempnam(sys_get_temp_dir(), 'export_');
        $handle = fopen($file, 'w');
        
        // En-têtes du fichier
        fputcsv($handle, [
            'ID',
            'Véhicule',
            'Date du contrôle',
            'Type de contrôle',
            'Résultat',
            'Kilométrage',
            'Commentaire',
            'Contrôle effectué par',
            'Date de création',
            'Dernière mise à jour'
        ], ';');
        
        // Données
        foreach ($controles as $controle) {
            fputcsv($handle, [
                $controle->id,
                $controle->vehicle ? $controle->vehicle->immatriculation : 'N/A',
                $controle->date_controle->format('d/m/Y'),
                $controle->type_controle,
                $this->formatResultat($controle->resultat),
                $controle->kilometrage,
                $controle->commentaire,
                $controle->user ? $controle->user->name : 'N/A',
                $controle->created_at->format('d/m/Y H:i'),
                $controle->updated_at->format('d/m/Y H:i')
            ], ';');
        }
        
        fclose($handle);
        
        // Télécharger le fichier
        return response()->download($file, $fileName, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ])->deleteFileAfterSend(true);
    }
    
    /**
     * Formate le résultat pour l'affichage
     */
    private function formatResultat($resultat)
    {
        $resultats = [
            'conforme' => 'Conforme',
            'non_conforme' => 'Non conforme',
            'en_attente' => 'En attente'
        ];
        
        return $resultats[$resultat] ?? $resultat;
    }
}
