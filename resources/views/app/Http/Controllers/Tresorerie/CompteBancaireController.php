<?php

namespace App\Http\Controllers\Tresorerie;

use App\Http\Controllers\Controller;
use App\Models\Banque;
use App\Models\CompteBancaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CompteBancaireController extends Controller
{
    /**
     * Liste des comptes bancaires (standalone)
     */
    public function index()
    {
        $comptes = CompteBancaire::with('banque')->latest()->get();
        return view('tresorerie.comptes-bancaires', compact('comptes'));
    }

    /**
     * Formulaire de création standalone (sans banque pré-sélectionnée)
     */
    public function createStandalone()
    {
        $banques = Banque::orderBy('nom')->get();
        return view('tresorerie.comptes-bancaires-create', compact('banques'));
    }

    /**
     * Enregistrer un compte bancaire (standalone)
     */
    public function storeStandalone(Request $request)
    {
        $validated = $request->validate([
            'reference' => 'nullable|string|max:255',
            'nom' => 'required|string|max:255',
            'banque' => 'required|string|max:255',
            'numero_compte' => 'required|string|max:50',
            'solde' => 'required|numeric|min:0',
            'devise' => 'required|string|max:10',
            'statut' => 'required|string',
            'responsable' => 'nullable|string|max:255',
            'date_ouverture' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        try {
            // Trouver ou créer la banque
            $banque = Banque::firstOrCreate(['nom' => $validated['banque']]);

            CompteBancaire::create([
                'banque_id' => $banque->id,
                'numero_compte' => $validated['numero_compte'],
                'intitule_compte' => $validated['nom'],
                'type_compte' => 'courant',
                'devise' => $validated['devise'],
                'solde' => $validated['solde'],
                'solde_ouverture' => $validated['solde'],
                'date_ouverture' => $validated['date_ouverture'],
                'nom_titulaire' => $validated['responsable'] ?? 'KENAM SERVICES',
                'est_actif' => $validated['statut'] === 'actif',
                'informations_supplementaires' => $validated['notes'] ?? null,
            ]);

            return redirect()->route('tresorerie.comptes-bancaires')
                ->with('success', 'Compte bancaire créé avec succès.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Erreur lors de la création : ' . $e->getMessage());
        }
    }

    /**
     * Afficher un compte bancaire (standalone)
     */
    public function showStandalone($id)
    {
        $compte = CompteBancaire::with('banque')->findOrFail($id);
        return view('tresorerie.comptes-bancaires-show', compact('compte'));
    }

    /**
     * Formulaire d'édition standalone
     */
    public function editStandalone($id)
    {
        $compte = CompteBancaire::with('banque')->findOrFail($id);
        $banques = Banque::orderBy('nom')->get();
        return view('tresorerie.comptes-bancaires-edit', compact('compte', 'banques'));
    }

    /**
     * Mettre à jour un compte bancaire (standalone)
     */
    public function updateStandalone(Request $request, $id)
    {
        $compte = CompteBancaire::findOrFail($id);
        $compte->update($request->all());
        return redirect()->route('tresorerie.comptes-bancaires')
            ->with('success', 'Compte bancaire mis à jour avec succès.');
    }

    /**
     * Supprimer un compte bancaire (standalone)
     */
    public function destroyStandalone($id)
    {
        CompteBancaire::findOrFail($id)->delete();
        return redirect()->route('tresorerie.comptes-bancaires')
            ->with('success', 'Compte bancaire supprimé avec succès.');
    }

    /**
     * Afficher le formulaire de création d'un nouveau compte bancaire (nested sous banque)
     *
     * @param  \App\Models\Banque  $banque
     * @return \Illuminate\View\View
     */
    public function create(Banque $banque)
    {
        $this->authorize('create', [CompteBancaire::class, $banque]);

        $typesCompte = CompteBancaire::TYPES_COMPTE;
        $devises = CompteBancaire::DEVISE;

        return view('tresorerie.banques.comptes.create', compact('banque', 'typesCompte', 'devises'));
    }

    /**
     * Enregistrer un nouveau compte bancaire
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Banque  $banque
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Banque $banque)
    {
        $this->authorize('create', [CompteBancaire::class, $banque]);

        $validated = $request->validate([
            'numero_compte' => [
                'required',
                'string',
                'max:50',
                Rule::unique('compte_bancaires')
                    ->where('banque_id', $banque->id)
                    ->whereNull('deleted_at')
            ],
            'intitule_compte' => 'required|string|max:255',
            'type_compte' => 'required|in:' . implode(',', array_keys(CompteBancaire::TYPES_COMPTE)),
            'devise' => 'required|in:' . implode(',', array_keys(CompteBancaire::DEVISE)),
            'solde_ouverture' => 'required|numeric|min:0',
            'date_ouverture' => 'required|date',
            'date_fermeture' => 'nullable|date|after_or_equal:date_ouverture',
            'nom_titulaire' => 'required|string|max:255',
            'adresse_titulaire' => 'nullable|string|max:255',
            'telephone_titulaire' => 'nullable|string|max:20',
            'email_titulaire' => 'nullable|email|max:255',
            'nom_contact' => 'nullable|string|max:255',
            'telephone_contact' => 'nullable|string|max:20',
            'email_contact' => 'nullable|email|max:255',
            'decouvert_autorise' => 'nullable|numeric|min:0',
            'taux_interet' => 'nullable|numeric|min:0|max:100',
            'informations_supplementaires' => 'nullable|string',
            'est_actif' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Créer le compte avec les données validées
            $compte = $banque->comptes()->create([
                'numero_compte' => $validated['numero_compte'],
                'intitule_compte' => $validated['intitule_compte'],
                'type_compte' => $validated['type_compte'],
                'devise' => $validated['devise'],
                'solde' => $validated['solde_ouverture'],
                'solde_ouverture' => $validated['solde_ouverture'],
                'date_ouverture' => $validated['date_ouverture'],
                'date_fermeture' => $validated['date_fermeture'] ?? null,
                'nom_titulaire' => $validated['nom_titulaire'],
                'adresse_titulaire' => $validated['adresse_titulaire'] ?? null,
                'telephone_titulaire' => $validated['telephone_titulaire'] ?? null,
                'email_titulaire' => $validated['email_titulaire'] ?? null,
                'nom_contact' => $validated['nom_contact'] ?? null,
                'telephone_contact' => $validated['telephone_contact'] ?? null,
                'email_contact' => $validated['email_contact'] ?? null,
                'decouvert_autorise' => $validated['decouvert_autorise'] ?? 0,
                'taux_interet' => $validated['taux_interet'] ?? 0,
                'informations_supplementaires' => $validated['informations_supplementaires'] ?? null,
                'est_actif' => $validated['est_actif'] ?? true,
            ]);

            DB::commit();

            return redirect()
                ->route('tresorerie.banques.show', $banque)
                ->with('success', 'Le compte bancaire a été créé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création du compte : ' . $e->getMessage());
        }
    }

    /**
     * Afficher le formulaire d'édition d'un compte bancaire
     *
     * @param  \App\Models\Banque  $banque
     * @param  \App\Models\CompteBancaire  $compte
     * @return \Illuminate\View\View
     */
    public function edit(Banque $banque, CompteBancaire $compte)
    {
        $this->authorize('update', $compte);

        $typesCompte = CompteBancaire::TYPES_COMPTE;
        $devises = CompteBancaire::DEVISE;

        return view('tresorerie.banques.comptes.edit', compact('banque', 'compte', 'typesCompte', 'devises'));
    }

    /**
     * Mettre à jour un compte bancaire
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Banque  $banque
     * @param  \App\Models\CompteBancaire  $compte
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Banque $banque, CompteBancaire $compte)
    {
        $this->authorize('update', $compte);

        $validated = $request->validate([
            'intitule_compte' => 'required|string|max:255',
            'type_compte' => 'required|in:' . implode(',', array_keys(CompteBancaire::TYPES_COMPTE)),
            'devise' => 'required|in:' . implode(',', array_keys(CompteBancaire::DEVISE)),
            'solde_ouverture' => 'required|numeric|min:0',
            'date_ouverture' => 'required|date',
            'date_fermeture' => 'nullable|date|after_or_equal:date_ouverture',
            'nom_titulaire' => 'required|string|max:255',
            'adresse_titulaire' => 'nullable|string|max:255',
            'telephone_titulaire' => 'nullable|string|max:20',
            'email_titulaire' => 'nullable|email|max:255',
            'nom_contact' => 'nullable|string|max:255',
            'telephone_contact' => 'nullable|string|max:20',
            'email_contact' => 'nullable|email|max:255',
            'decouvert_autorise' => 'nullable|numeric|min:0',
            'taux_interet' => 'nullable|numeric|min:0|max:100',
            'informations_supplementaires' => 'nullable|string',
            'est_actif' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Mettre à jour le compte avec les données validées
            $compte->update([
                'intitule_compte' => $validated['intitule_compte'],
                'type_compte' => $validated['type_compte'],
                'devise' => $validated['devise'],
                'solde_ouverture' => $validated['solde_ouverture'],
                'date_ouverture' => $validated['date_ouverture'],
                'date_fermeture' => $validated['date_fermeture'] ?? null,
                'nom_titulaire' => $validated['nom_titulaire'],
                'adresse_titulaire' => $validated['adresse_titulaire'] ?? null,
                'telephone_titulaire' => $validated['telephone_titulaire'] ?? null,
                'email_titulaire' => $validated['email_titulaire'] ?? null,
                'nom_contact' => $validated['nom_contact'] ?? null,
                'telephone_contact' => $validated['telephone_contact'] ?? null,
                'email_contact' => $validated['email_contact'] ?? null,
                'decouvert_autorise' => $validated['decouvert_autorise'] ?? 0,
                'taux_interet' => $validated['taux_interet'] ?? 0,
                'informations_supplementaires' => $validated['informations_supplementaires'] ?? null,
                'est_actif' => $validated['est_actif'] ?? true,
            ]);

            DB::commit();

            return redirect()
                ->route('tresorerie.banques.show', $banque)
                ->with('success', 'Le compte bancaire a été mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour du compte : ' . $e->getMessage());
        }
    }

    /**
     * Supprimer un compte bancaire
     *
     * @param  \App\Models\Banque  $banque
     * @param  \App\Models\CompteBancaire  $compte
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Banque $banque, CompteBancaire $compte)
    {
        $this->authorize('delete', $compte);

        if ($compte->operations()->exists()) {
            return back()
                ->with('error', 'Impossible de supprimer ce compte car il contient des opérations.');
        }

        try {
            $compte->delete();

            return redirect()
                ->route('tresorerie.banques.show', $banque)
                ->with('success', 'Le compte bancaire a été supprimé avec succès.');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Une erreur est survenue lors de la suppression du compte : ' . $e->getMessage());
        }
    }
}
