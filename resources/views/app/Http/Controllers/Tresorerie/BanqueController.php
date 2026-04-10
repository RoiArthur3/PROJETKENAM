<?php

namespace App\Http\Controllers\Tresorerie;

use App\Http\Controllers\Controller;
use App\Models\Banque;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BanqueController extends Controller
{
    /**
     * Afficher le formulaire de création d'une banque
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('tresorerie.banques.create');
    }

    /**
     * Enregistrer une nouvelle banque
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:banques',
            'code_banque' => 'nullable|string|max:50|unique:banques',
            'code_guichet' => 'nullable|string|max:50',
            'adresse' => 'nullable|string|max:255',
            'ville' => 'nullable|string|max:100',
            'pays' => 'nullable|string|max:100',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'site_web' => 'nullable|url|max:255',
            'est_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $banque = Banque::create($validated);

            DB::commit();

            return redirect()
                ->route('tresorerie.banques.show', $banque)
                ->with('success', 'La banque a été créée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création de la banque : ' . $e->getMessage());
        }
    }

    /**
     * Afficher les détails d'une banque
     *
     * @param  \App\Models\Banque  $banque
     * @return \Illuminate\View\View
     */
    public function show(Banque $banque)
    {
        $banque->load('comptes');
        return view('tresorerie.banques.show', compact('banque'));
    }

    /**
     * Afficher le formulaire d'édition d'une banque
     *
     * @param  \App\Models\Banque  $banque
     * @return \Illuminate\View\View
     */
    public function edit(Banque $banque)
    {
        return view('tresorerie.banques.edit', compact('banque'));
    }

    /**
     * Mettre à jour une banque
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Banque  $banque
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Banque $banque)
    {
        $validated = $request->validate([
            'nom' => [
                'required',
                'string',
                'max:255',
                Rule::unique('banques')->ignore($banque->id),
            ],
            'code_banque' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('banques')->ignore($banque->id),
            ],
            'code_guichet' => 'nullable|string|max:50',
            'adresse' => 'nullable|string|max:255',
            'ville' => 'nullable|string|max:100',
            'pays' => 'nullable|string|max:100',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'site_web' => 'nullable|url|max:255',
            'est_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $banque->update($validated);

            DB::commit();

            return redirect()
                ->route('tresorerie.banques.show', $banque)
                ->with('success', 'Les informations de la banque ont été mises à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour de la banque : ' . $e->getMessage());
        }
    }

    /**
     * Supprimer une banque
     *
     * @param  \App\Models\Banque  $banque
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Banque $banque)
    {
        if ($banque->comptes()->exists()) {
            return back()
                ->with('error', 'Impossible de supprimer cette banque car elle contient des comptes bancaires.');
        }

        try {
            $banque->delete();
            
            return redirect()
                ->route('tresorerie.banques.index')
                ->with('success', 'La banque a été supprimée avec succès.');
                
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Une erreur est survenue lors de la suppression de la banque : ' . $e->getMessage());
        }
    }
}
