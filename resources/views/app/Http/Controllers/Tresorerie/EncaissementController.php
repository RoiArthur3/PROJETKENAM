<?php

namespace App\Http\Controllers\Tresorerie;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Encaissement;
use App\Models\Caisse;
use App\Models\Invoice;
use App\Models\Operation;
use App\Models\Project;
use App\Models\Client;
use App\Models\Fournisseur;
use App\Models\MouvementCaisse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EncaissementController extends Controller
{
    public function index()
    {
        $encaissements = Encaissement::with(['caisse', 'creator', 'invoice', 'operation', 'project', 'clientRel'])
            ->orderBy('date_encaissement', 'desc')
            ->get();

        return view('tresorerie.encaissements', compact('encaissements'));
    }

    public function create()
    {
        $caisses = Caisse::where('est_active', true)->orderBy('nom')->get();
        
        // Invoices not fully paid
        $invoices = Invoice::where('status', '!=', 'paid')
            ->with('client')
            ->latest()
            ->get();
            
        // Operations approved
        $operations = Operation::where('statut_courant', 'approuvee')
            ->latest()
            ->limit(50)
            ->get();
            
        // Projects active
        $projects = Project::whereIn('statut', ['en_cours', 'valide'])
            ->latest()
            ->get();
            
        $clients = Client::orderBy('nom')->get();
        $fournisseurs = Fournisseur::orderBy('nom')->get();
        
        return view('tresorerie.encaissements-create', compact(
            'caisses', 
            'invoices', 
            'operations', 
            'projects', 
            'clients', 
            'fournisseurs'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date_encaissement' => 'required|date',
            'type_encaissement' => 'required|string',
            'montant' => 'required|numeric|min:0',
            'caisse_id' => 'required|exists:caisses,id',
            'mode_paiement' => 'required|string',
            'description' => 'required|string|max:500',
            'invoice_id' => 'nullable|exists:invoices,id',
            'operation_id' => 'nullable|exists:operations,id',
            'project_id' => 'nullable|exists:projects,id',
            'client_id' => 'nullable|exists:clients,id',
            'fournisseur_id' => 'nullable|exists:fournisseurs,id',
            'reference_externe' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'beneficiaire' => 'nullable|string|max:255', // Legacy field or manual entry
        ]);

        $user = Auth::user();

        try {
            DB::beginTransaction();

            $reference = 'ENC-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            $encaissement = Encaissement::create([
                'invoice_id' => $validated['invoice_id'] ?? null,
                'operation_id' => $validated['operation_id'] ?? null,
                'project_id' => $validated['project_id'] ?? null,
                'client_id' => $validated['client_id'] ?? null,
                'fournisseur_id' => $validated['fournisseur_id'] ?? null,
                'reference' => $reference,
                'reference_externe' => $validated['reference_externe'] ?? null,
                'date_encaissement' => $validated['date_encaissement'],
                'type_encaissement' => $validated['type_encaissement'],
                'montant' => $validated['montant'],
                'mode_paiement' => $validated['mode_paiement'],
                'client' => $validated['beneficiaire'] ?? null,
                'caisse_id' => $validated['caisse_id'],
                'description' => $validated['description'],
                'notes' => $validated['notes'] ?? null,
                'statut' => 'validé',
                'created_by' => $user->id,
            ]);

            // 1. Update Caisse Balance
            $caisse = Caisse::findOrFail($validated['caisse_id']);
            $caisse->increment('solde_actuel', $validated['montant']);

            // 2. Log Movement
            MouvementCaisse::create([
                'caisse_id' => $caisse->id,
                'reference' => $reference,
                'date_mouvement' => $validated['date_encaissement'],
                'type_mouvement' => 'entree',
                'montant' => $validated['montant'],
                'devise' => $caisse->devise ?? 'XOF',
                'libelle' => 'Encaissement: ' . $validated['type_encaissement'],
                'description' => $validated['description'],
                'statut' => 'validé',
                'created_by' => $user->id,
                'model_type' => Encaissement::class,
                'model_id' => $encaissement->id,
            ]);

            // 3. Link to Invoice if provided
            if (!empty($validated['invoice_id'])) {
                $invoice = Invoice::find($validated['invoice_id']);
                if ($invoice) {
                    // Create a payment record as well to sync with Invoice system
                    \App\Models\Payment::create([
                        'invoice_id' => $invoice->id,
                        'payment_date' => $validated['date_encaissement'],
                        'amount_paid' => $validated['montant'],
                        'payment_method' => $validated['mode_paiement'],
                        'reference' => $reference,
                        'notes' => 'Généré via Encaissement Trésorerie: ' . $encaissement->reference,
                    ]);
                    
                    // The Invoice model likely needs its status updated if fully paid
                    // Logic for status update should ideally be in model or observer, 
                    // but we can check here
                    $totalPaid = $invoice->payments()->sum('amount_paid');
                    if ($totalPaid >= $invoice->net_amount) {
                        $invoice->update(['status' => 'paid']);
                    } else if ($totalPaid > 0) {
                        $invoice->update(['status' => 'partially_paid']);
                    }
                }
            }

            DB::commit();

            return redirect()->route('tresorerie.encaissements')
                ->with('success', 'Encaissement enregistré avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Erreur lors de l\'enregistrement : ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $encaissement = Encaissement::with(['caisse', 'creator', 'invoice', 'operation', 'project', 'clientRel', 'fournisseur'])
            ->findOrFail($id);
        return view('tresorerie.encaissements-show', compact('encaissement'));
    }
}
