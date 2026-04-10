<?php

namespace App\Http\Controllers\Tresorerie;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DepenseCaisseController extends Controller
{
    public function index()
    {
        $depenses = \App\Models\DepenseCaisse::latest()->get();
        return view('tresorerie.depenses', compact('depenses'));
    }

    public function create()
    {
        $caisses = \App\Models\Caisse::where('est_active', true)->orderBy('nom')->get();
        // Récupérer les opérations approuvées non payées
        $operations = \App\Models\Operation::where('statut_courant', 'approuvee')
            ->where(function($q) {
                $q->where('is_paid', false)->orWhereNull('is_paid');
            })
            ->latest()
            ->get();
            
        return view('tresorerie.decaissements-create', compact('caisses', 'operations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'operation_id' => 'nullable|exists:operations,id',
            'reference' => 'required|string|max:255',
            'date_decaissement' => 'required|date',
            'libelle' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'caisse_id' => 'required|exists:caisses,id',
            'statut' => 'required|string',
            'beneficiaire' => 'required|string|max:255',
            'motif' => 'nullable|string|max:255',
            'responsable' => 'nullable|string|max:255',
            'justification' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $user = \Auth::user();

        \DB::transaction(function() use ($validated, $user) {
            $depense = \App\Models\DepenseCaisse::create([
                'operation_id' => $validated['operation_id'] ?? null,
                'reference' => $validated['reference'],
                'date_depense' => $validated['date_decaissement'],
                'libelle' => $validated['libelle'],
                'montant' => $validated['montant'],
                'caisse_id' => $validated['caisse_id'],
                'statut' => $validated['statut'],
                'notes' => ($validated['notes'] ?? '') . "\nBénéficiaire: " . $validated['beneficiaire'] . ($validated['motif'] ? "\nMotif: " . $validated['motif'] : ""),
                'created_by' => $user->id,
                'createur_id' => $user->id,
            ]);

            // Si une opération est liée, on la marque comme payée
            if (!empty($validated['operation_id'])) {
                $operation = \App\Models\Operation::find($validated['operation_id']);
                if ($operation) {
                    $operation->update([
                        'is_paid' => true,
                        'paid_at' => now(),
                        'paid_by' => $user->id,
                        'payment_reference' => $validated['reference']
                    ]);
                    
                    // Log de statut
                    \App\Models\OperationStatusLog::create([
                        'operation_id' => $operation->id,
                        'from_status' => $operation->statut_courant,
                        'to_status' => 'payee',
                        'user_name' => $user->name,
                        'user_id' => $user->id,
                        'commentaire' => 'Décaissement effectué via caisse: ' . $depense->reference,
                    ]);
                }
            }
        });

        return redirect()->route('tresorerie.decaissements.show', $depense->id)
            ->with('success', 'Décaissement enregistré avec succès. Vous pouvez maintenant l\'imprimer.');
    }

    public function show($id)
    {
        $decaissement = \App\Models\DepenseCaisse::with(['operation', 'caisse'])->findOrFail($id);
        return view('tresorerie.decaissements-show', compact('decaissement'));
    }

    public function imprimer($id)
    {
        $decaissement = \App\Models\DepenseCaisse::with(['operation', 'caisse'])->findOrFail($id);
        
        // On récupère les paramètres de l'entreprise
        $entreprise = \App\Models\EntrepriseSettings::getActive();
        
        return view('tresorerie.decaissements-print', compact('decaissement', 'entreprise'));
    }

    public function edit($id)
    {
        $decaissement = \App\Models\DepenseCaisse::findOrFail($id);
        $caisses = \App\Models\Caisse::where('est_active', true)->orderBy('nom')->get();
        return view('tresorerie.decaissements-edit', compact('decaissement', 'caisses'));
    }

    public function update(Request $request, $id)
    {
        $decaissement = \App\Models\DepenseCaisse::findOrFail($id);
        $decaissement->update($request->all());
        return redirect()->route('tresorerie.decaissements')
            ->with('success', 'Décaissement mis à jour avec succès');
    }

    public function destroy($id)
    {
        \App\Models\DepenseCaisse::findOrFail($id)->delete();
        return redirect()->route('tresorerie.decaissements')
            ->with('success', 'Décaissement supprimé avec succès');
    }
}
