<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use App\Models\PersonnelContrat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PersonnelContratController extends Controller
{
    public function index()
    {
        $contrats = PersonnelContrat::with('personnel')->orderBy('created_at', 'desc')->get();
        return view('rh.personnel.contrats.index', compact('contrats'));
    }

    public function create(Request $request)
    {
        $personnel = null;
        if ($request->has('personnel_id')) {
            $personnel = Personnel::findOrFail($request->personnel_id);
        }
        
        $personnels = Personnel::all();
        return view('rh.personnel.contrats.create', compact('personnel', 'personnels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'personnel_id' => 'required|exists:personnel,id',
            'numero_contrat' => 'required|string|unique:personnel_contrats',
            'type_contrat' => 'required|in:CDI,CDD,STAGE,INTERIM,CONSULTANT,PRESTATAIRE',
            'date_debut' => 'required|date',
            'date_fin' => 'required_if:type_contrat,CDD,STAGE,INTERIM|nullable|date|after_or_equal:date_debut',
            'duree_essai_jours' => 'nullable|integer|min:0',
            'poste' => 'required|string|max:255',
            'salaire_base' => 'required|numeric|min:0',
            'devise' => 'required|string|max:10',
            'frequence_paiement' => 'required|string',
            'lieu_travail' => 'nullable|string',
            'description_taches' => 'nullable|string',
            'avantages' => 'nullable|string',
            'statut' => 'required|in:PROJET,SIGNE,ACTIF,TERMINE',
        ]);

        try {
            DB::beginTransaction();

            if ($validated['duree_essai_jours']) {
                 $validated['fin_periode_essai'] = Carbon::parse($validated['date_debut'])
                    ->addDays((int) $validated['duree_essai_jours']);
            }

            $validated['created_by'] = Auth::id();
            
            $contrat = PersonnelContrat::create($validated);

            // Optionnel : Mettre à jour le statut du personnel
            $personnel = Personnel::find($validated['personnel_id']);
            if ($validated['statut'] === 'ACTIF' || $validated['statut'] === 'SIGNE') {
                $personnel->update([
                    'statut' => 'ACTIF',
                    'type_contrat' => $validated['type_contrat'],
                    'date_embauche' => $validated['date_debut'],
                    'date_fin_contrat' => $validated['date_fin'],
                    'salaire_base' => $validated['salaire_base']
                ]);
            }

            DB::commit();

            return redirect()->route('rh.personnel.show', $validated['personnel_id'])
                ->with('success', 'Contrat créé avec succès pour ' . $personnel->nom_complet);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Erreur lors de la création du contrat : ' . $e->getMessage());
        }
    }

    public function show(PersonnelContrat $contrat)
    {
        $contrat->load('personnel');
        return view('rh.personnel.contrats.show', compact('contrat'));
    }

    public function edit(PersonnelContrat $contrat)
    {
        $contrat->load('personnel');
        $personnels = Personnel::all();
        return view('rh.personnel.contrats.edit', compact('contrat', 'personnels'));
    }

    public function update(Request $request, PersonnelContrat $contrat)
    {
        $validated = $request->validate([
            'numero_contrat' => 'required|string|unique:personnel_contrats,numero_contrat,' . $contrat->id,
            'type_contrat' => 'required|in:CDI,CDD,STAGE,INTERIM,CONSULTANT,PRESTATAIRE',
            'date_debut' => 'required|date',
            'date_fin' => 'required_if:type_contrat,CDD,STAGE,INTERIM|nullable|date|after_or_equal:date_debut',
            'duree_essai_jours' => 'nullable|integer|min:0',
            'poste' => 'required|string|max:255',
            'salaire_base' => 'required|numeric|min:0',
            'devise' => 'required|string|max:10',
            'frequence_paiement' => 'required|string',
            'statut' => 'required|in:PROJET,SIGNE,ACTIF,SUSPENDU,TERMINE,RESILIE',
        ]);

        try {
            DB::beginTransaction();

            if ($validated['duree_essai_jours']) {
                 $validated['fin_periode_essai'] = Carbon::parse($validated['date_debut'])
                    ->addDays((int) $validated['duree_essai_jours']);
            }

            $validated['updated_by'] = Auth::id();
            
            $contrat->update($validated);

            DB::commit();

            return redirect()->route('rh.personnel.show', $contrat->personnel_id)
                ->with('success', 'Contrat mis à jour avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }
}
