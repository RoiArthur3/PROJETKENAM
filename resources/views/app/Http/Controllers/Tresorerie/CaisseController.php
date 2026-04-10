<?php

namespace App\Http\Controllers\Tresorerie;

use App\Http\Controllers\Controller;
use App\Models\Caisse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CaisseController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Caisse::class);

        $libelleColumn = Schema::hasColumn('caisses', 'nom')
            ? 'nom'
            : (Schema::hasColumn('caisses', 'libelle') ? 'libelle' : 'nom');

        $caisses = Caisse::with('responsable')
            ->orderBy('type')
            ->orderBy($libelleColumn)
            ->get();

        return view('tresorerie.caisses.index', compact('caisses'));
    }

    public function create()
    {
        $this->authorize('create', Caisse::class);

        $responsables = User::where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('tresorerie.caisses.create', compact('responsables'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Caisse::class);

        $libelleColumn = Schema::hasColumn('caisses', 'nom') ? 'nom' : (Schema::hasColumn('caisses', 'libelle') ? 'libelle' : 'nom');

        $validated = $request->validate([
            'nom' => [
                'required',
                'string',
                'max:255',
                Rule::unique('caisses', $libelleColumn),
            ],
            'type' => 'required|in:principale,secondaire',
            'solde_initial' => 'required|numeric|min:0',
            'devise' => 'required|string|max:10',
            'responsable_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'est_active' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            $payload = [
                'type' => $validated['type'],
                'solde_initial' => $validated['solde_initial'],
                'solde_actuel' => $validated['solde_initial'],
                'devise' => $validated['devise'],
                'responsable_id' => $validated['responsable_id'] ?? null,
                'description' => $validated['description'] ?? null,
                'est_active' => $request->boolean('est_active'),
            ];

            if (Schema::hasColumn('caisses', 'code')) {
                $payload['code'] = 'CAI-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
            }

            if (Schema::hasColumn('caisses', 'nom')) {
                $payload['nom'] = $validated['nom'];
            }

            if (Schema::hasColumn('caisses', 'libelle')) {
                $payload['libelle'] = $validated['nom'];
            }

            $caisse = Caisse::create($payload);

            if (Schema::hasColumn('caisses', 'libelle')) {
                $caisse->libelle = $validated['nom'];
            }

            if (Schema::hasColumn('caisses', 'code') && empty($caisse->code)) {
                $caisse->code = 'CAI-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
            }

            if ($caisse->isDirty()) {
                $caisse->save();
            }

            DB::commit();

            return redirect()
                ->route('tresorerie.caisses.index')
                ->with('success', 'La caisse a été créée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création de la caisse: ' . $e->getMessage());
        }
    }

    public function show(Caisse $caisse)
    {
        $this->authorize('view', $caisse);

        $caisse->load([
            'responsable',
            'mouvements' => function ($query) {
                $query->latest()->take(10);
            },
            'mouvements.createur'
        ]);

        return view('tresorerie.caisses.show', compact('caisse'));
    }

    public function edit(Caisse $caisse)
    {
        $this->authorize('update', $caisse);

        $responsables = User::where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('tresorerie.caisses.edit', compact('caisse', 'responsables'));
    }

    public function update(Request $request, Caisse $caisse)
    {
        $this->authorize('update', $caisse);

        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:caisses,nom,' . $caisse->id,
            'type' => 'required|in:principale,secondaire',
            'solde_initial' => 'required|numeric|min:0',
            'devise' => 'required|string|max:10',
            'responsable_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'est_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $ancienSolde = $caisse->solde_initial;
            $nouveauSolde = $validated['solde_initial'];
            $difference = $nouveauSolde - $ancienSolde;

            $caisse->update([
                'nom' => $validated['nom'],
                'type' => $validated['type'],
                'solde_initial' => $nouveauSolde,
                'solde_actuel' => $caisse->solde_actuel + $difference,
                'devise' => $validated['devise'],
                'responsable_id' => $validated['responsable_id'] ?? null,
                'description' => $validated['description'] ?? null,
                'est_active' => $validated['est_active'] ?? true,
            ]);

            // Si le solde a changé, enregistrer un mouvement de régularisation
            if ($difference != 0) {
                $caisse->mouvements()->create([
                    'type_mouvement' => 'regularisation',
                    'montant' => abs($difference),
                    'description' => $difference > 0
                        ? 'Ajustement à la hausse du solde initial'
                        : 'Ajustement à la baisse du solde initial',
                    'created_by' => auth()->id(),
                ]);
            }

            DB::commit();

            return redirect()
                ->route('tresorerie.caisses.show', $caisse)
                ->with('success', 'La caisse a été mise à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour de la caisse: ' . $e->getMessage());
        }
    }

    public function destroy(Caisse $caisse)
    {
        $this->authorize('delete', $caisse);

        if ($caisse->mouvements()->exists()) {
            return back()
                ->with('error', 'Impossible de supprimer cette caisse car elle contient des mouvements.');
        }

        $caisse->delete();

        return redirect()
            ->route('tresorerie.caisses.index')
            ->with('success', 'La caisse a été supprimée avec succès.');
    }

    // Méthodes API

    public function getSolde(Caisse $caisse)
    {
        $this->authorize('view', $caisse);

        return response()->json([
            'solde' => $caisse->solde_actuel,
            'devise' => $caisse->devise,
        ]);
    }

    public function transfert(Request $request, Caisse $caisse)
    {
        $this->authorize('update', $caisse);

        $validated = $request->validate([
            'montant' => 'required|numeric|min:0.01',
            'caisse_destination_id' => 'required|exists:caisses,id',
            'motif' => 'required|string|max:255',
        ]);

        $caisseDestination = Caisse::findOrFail($validated['caisse_destination_id']);

        if ($caisse->id === $caisseDestination->id) {
            return response()->json([
                'success' => false,
                'message' => 'La caisse source et la caisse destination doivent être différentes.',
            ], 422);
        }

        if ($caisse->solde_actuel < $validated['montant']) {
            return response()->json([
                'success' => false,
                'message' => 'Le solde de la caisse source est insuffisant pour effectuer ce transfert.',
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Créer l'approvisionnement
            $approvisionnement = $caisse->approvisionnementsSource()->create([
                'caisse_destination_id' => $caisseDestination->id,
                'montant' => $validated['montant'],
                'statut' => 'valide',
                'motif' => $validated['motif'],
                'demandeur_id' => auth()->id(),
                'validateur_id' => auth()->id(),
                'date_validation' => now(),
                'date_decaissement' => now(),
            ]);

            // Mettre à jour les soldes
            $caisse->decrement('solde_actuel', $validated['montant']);
            $caisseDestination->increment('solde_actuel', $validated['montant']);

            // Enregistrer les mouvements
            $caisse->mouvements()->create([
                'type_mouvement' => 'approvisionnement',
                'montant' => -$validated['montant'],
                'reference_type' => get_class($approvisionnement),
                'reference_id' => $approvisionnement->id,
                'description' => 'Transfert vers ' . $caisseDestination->nom . ' - ' . $validated['motif'],
                'created_by' => auth()->id(),
            ]);

            $caisseDestination->mouvements()->create([
                'type_mouvement' => 'approvisionnement',
                'montant' => $validated['montant'],
                'reference_type' => get_class($approvisionnement),
                'reference_id' => $approvisionnement->id,
                'description' => 'Réception de ' . $caisse->nom . ' - ' . $validated['motif'],
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Le transfert a été effectué avec succès.',
                'data' => [
                    'solde_source' => $caisse->fresh()->solde_actuel,
                    'solde_destination' => $caisseDestination->fresh()->solde_actuel,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors du transfert: ' . $e->getMessage(),
            ], 500);
        }
    }
}
