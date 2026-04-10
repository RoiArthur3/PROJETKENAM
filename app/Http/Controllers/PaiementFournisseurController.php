<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use App\Models\PaiementFournisseur;
use App\Models\FactureFournisseur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use PDF;

class PaiementFournisseurController extends Controller
{
    /**
     * Afficher la liste des paiements d'un fournisseur
     */
    public function index(Fournisseur $fournisseur, Request $request)
    {
        $query = $fournisseur->paiements()
            ->with(['facture', 'user'])
            ->latest('date_paiement');
        
        // Filtres
        if ($request->filled('mode_paiement')) {
            $query->where('mode_paiement', $request->input('mode_paiement'));
        }
        
        if ($request->filled('facture_id')) {
            $query->where('facture_id', $request->input('facture_id'));
        }
        
        if ($request->filled('debut') && $request->filled('fin')) {
            $query->whereBetween('date_paiement', [
                $request->input('debut'),
                $request->input('fin')
            ]);
        }
        
        if ($request->filled('recherche')) {
            $recherche = $request->input('recherche');
            $query->where(function($q) use ($recherche) {
                $q->where('reference', 'like', "%{$recherche}%")
                  ->orWhere('mode_paiement', 'like', "%{$recherche}%");
            });
        }
        
        $paiements = $query->paginate(15)->withQueryString();
        
        // Statistiques
        $stats = [
            'total' => $fournisseur->paiements()->count(),
            'montant_total' => $fournisseur->paiements()->sum('montant'),
            'moyenne_mensuelle' => $fournisseur->paiements()
                ->whereBetween('date_paiement', [now()->startOfYear(), now()->endOfYear()])
                ->select(DB::raw('SUM(montant) as total, DATE_FORMAT(date_paiement, "%Y-%m") as mois'))
                ->groupBy('mois')
                ->avg('total'),
            'par_mode_paiement' => $fournisseur->paiements()
                ->select('mode_paiement', DB::raw('SUM(montant) as total'))
                ->groupBy('mode_paiement')
                ->pluck('total', 'mode_paiement'),
        ];
        
        // Factures impayées ou partiellement payées
        $factures = $fournisseur->factures()
            ->whereIn('statut', ['non_payee', 'partiellement_payee'])
            ->orderBy('date_echeance')
            ->get();
        
        // Modes de paiement disponibles
        $modesPaiement = [
            'virement' => 'Virement bancaire',
            'cheque' => 'Chèque',
            'especes' => 'Espèces',
            'carte' => 'Carte bancaire',
            'prelevement' => 'Prélèvement',
            'autre' => 'Autre',
        ];
        
        return view('fournisseurs.paiements.index', compact(
            'fournisseur', 
            'paiements', 
            'stats',
            'factures',
            'modesPaiement'
        ));
    }

    /**
     * Afficher le formulaire de création d'un paiement
     */
    public function create(Fournisseur $fournisseur, Request $request)
    {
        $facture = null;
        $montantRestant = 0;
        
        // Si une facture est spécifiée, charger ses informations
        if ($request->filled('facture_id')) {
            $facture = $fournisseur->factures()
                ->with(['paiements'])
                ->findOrFail($request->input('facture_id'));
            
            $montantRestant = $facture->reste_a_payer;
        }
        
        // Factures impayées ou partiellement payées
        $factures = $fournisseur->factures()
            ->whereIn('statut', ['non_payee', 'partiellement_payee'])
            ->orderBy('date_echeance')
            ->get();
        
        // Modes de paiement disponibles
        $modesPaiement = [
            'virement' => 'Virement bancaire',
            'cheque' => 'Chèque',
            'especes' => 'Espèces',
            'carte' => 'Carte bancaire',
            'prelevement' => 'Prélèvement',
            'autre' => 'Autre',
        ];
        
        return view('fournisseurs.paiements.create', compact(
            'fournisseur',
            'facture',
            'factures',
            'montantRestant',
            'modesPaiement'
        ));
    }

    /**
     * Enregistrer un nouveau paiement
     */
    public function store(Request $request, Fournisseur $fournisseur)
    {
        $validated = $request->validate([
            'reference' => 'nullable|string|max:50|unique:paiement_fournisseurs,reference',
            'facture_id' => 'required|exists:facture_fournisseurs,id,fournisseur_id,' . $fournisseur->id,
            'date_paiement' => 'required|date',
            'montant' => 'required|numeric|min:0.01',
            'mode_paiement' => 'required|string|max:50',
            'reference_paiement' => 'nullable|string|max:100',
            'date_encaissement' => 'nullable|date|after_or_equal:date_paiement',
            'est_encaisse' => 'boolean',
            'notes' => 'nullable|string',
        ]);
        
        // Vérifier que le montant ne dépasse pas le montant restant de la facture
        $facture = FactureFournisseur::findOrFail($validated['facture_id']);
        $montantRestant = $facture->reste_a_payer;
        
        if ($validated['montant'] > $montantRestant) {
            return back()
                ->withInput()
                ->with('error', 'Le montant du paiement ne peut pas dépasser le montant restant à payer (' . number_format($montantRestant, 2, ',', ' ') . ' ' . config('app.devise', 'FCFA') . ').');
        }
        
        // Création du paiement
        $paiement = new PaiementFournisseur([
            'reference' => $validated['reference'] ?? 'PAY-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
            'fournisseur_id' => $fournisseur->id,
            'facture_id' => $validated['facture_id'],
            'date_paiement' => $validated['date_paiement'],
            'montant' => $validated['montant'],
            'mode_paiement' => $validated['mode_paiement'],
            'reference_paiement' => $validated['reference_paiement'] ?? null,
            'date_encaissement' => $validated['est_encaisse'] ? ($validated['date_encaissement'] ?? now()) : null,
            'est_encaisse' => $validated['est_encaisse'] ?? false,
            'notes' => $validated['notes'] ?? null,
            'user_id' => auth()->id(),
        ]);
        
        // Enregistrement dans une transaction
        DB::beginTransaction();
        
        try {
            $paiement->save();
            
            // Mise à jour de la facture
            $facture->montant_paye += $validated['montant'];
            $facture->reste_a_payer = $facture->montant_ttc - $facture->montant_paye;
            
            if ($facture->reste_a_payer <= 0) {
                $facture->statut = 'payee';
                $facture->date_paiement = $validated['date_paiement'];
            } else {
                $facture->statut = 'partiellement_payee';
            }
            
            $facture->save();
            
            // Mise à jour du fournisseur
            $fournisseur->total_paiements += $validated['montant'];
            $fournisseur->save();
            
            DB::commit();
            
            return redirect()
                ->route('fournisseurs.paiements.show', ['fournisseur' => $fournisseur->id, 'paiement' => $paiement->id])
                ->with('success', 'Le paiement a été enregistré avec succès.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement du paiement : ' . $e->getMessage());
        }
    }

    /**
     * Afficher les détails d'un paiement
     */
    public function show(Fournisseur $fournisseur, PaiementFournisseur $paiement)
    {
        // Vérifier que le paiement appartient bien au fournisseur
        if ($paiement->fournisseur_id !== $fournisseur->id) {
            abort(404);
        }
        
        $paiement->load(['facture', 'user', 'validateur']);
        
        return view('fournisseurs.paiements.show', compact(
            'fournisseur',
            'paiement'
        ));
    }

    /**
     * Marquer un paiement comme encaissé
     */
    public function encaisser(Request $request, Fournisseur $fournisseur, PaiementFournisseur $paiement)
    {
        // Vérifier que le paiement appartient bien au fournisseur
        if ($paiement->fournisseur_id !== $fournisseur->id) {
            abort(404);
        }
        
        // Vérifier que le paiement n'est pas déjà encaissé
        if ($paiement->est_encaisse) {
            return back()->with('warning', 'Ce paiement a déjà été marqué comme encaissé.');
        }
        
        $validated = $request->validate([
            'date_encaissement' => 'required|date|after_or_equal:' . $paiement->date_paiement,
            'notes' => 'nullable|string',
        ]);
        
        // Mise à jour du paiement
        $paiement->date_encaissement = $validated['date_encaissement'];
        $paiement->est_encaisse = true;
        $paiement->validateur_id = auth()->id();
        $paiement->notes = $validated['notes'] ?? $paiement->notes;
        
        try {
            $paiement->save();
            
            return back()->with('success', 'Le paiement a été marqué comme encaissé avec succès.');
            
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Une erreur est survenue lors de la mise à jour du paiement : ' . $e->getMessage());
        }
    }

    /**
     * Annuler un paiement
     */
    public function annuler(Request $request, Fournisseur $fournisseur, PaiementFournisseur $paiement)
    {
        // Vérifier que le paiement appartient bien au fournisseur
        if ($paiement->fournisseur_id !== $fournisseur->id) {
            abort(404);
        }
        
        // Vérifier que le paiement n'est pas déjà annulé
        if ($paiement->est_annule) {
            return back()->with('warning', 'Ce paiement a déjà été annulé.');
        }
        
        $validated = $request->validate([
            'motif_annulation' => 'required|string|max:255',
        ]);
        
        // Mise à jour du paiement
        $paiement->est_annule = true;
        $paiement->motif_annulation = $validated['motif_annulation'];
        $paiement->validateur_id = auth()->id();
        
        // Enregistrement dans une transaction
        DB::beginTransaction();
        
        try {
            $paiement->save();
            
            // Mise à jour de la facture
            $facture = $paiement->facture;
            $facture->montant_paye -= $paiement->montant;
            $facture->reste_a_payer += $paiement->montant;
            
            if ($facture->montant_paye <= 0) {
                $facture->statut = 'non_payee';
                $facture->date_paiement = null;
            } else {
                $facture->statut = 'partiellement_payee';
            }
            
            $facture->save();
            
            // Mise à jour du fournisseur
            $fournisseur->total_paiements -= $paiement->montant;
            $fournisseur->save();
            
            DB::commit();
            
            return back()->with('success', 'Le paiement a été annulé avec succès.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->with('error', 'Une erreur est survenue lors de l\'annulation du paiement : ' . $e->getMessage());
        }
    }

    /**
     * Générer un reçu de paiement au format PDF
     */
    public function recuPdf(Fournisseur $fournisseur, PaiementFournisseur $paiement)
    {
        // Vérifier que le paiement appartient bien au fournisseur
        if ($paiement->fournisseur_id !== $fournisseur->id) {
            abort(404);
        }
        
        $paiement->load(['facture', 'user']);
        
        $pdf = PDF::loadView('fournisseurs.paiements.recu-pdf', [
            'fournisseur' => $fournisseur,
            'paiement' => $paiement,
        ]);
        
        return $pdf->download('recu-paiement-' . $paiement->reference . '.pdf');
    }

    /**
     * Générer un état des paiements au format PDF
     */
    public function etatPaiementsPdf(Fournisseur $fournisseur, Request $request)
    {
        $query = $fournisseur->paiements()
            ->with(['facture'])
            ->latest('date_paiement');
        
        // Filtres
        if ($request->filled('debut') && $request->filled('fin')) {
            $query->whereBetween('date_paiement', [
                $request->input('debut'),
                $request->input('fin')
            ]);
        }
        
        if ($request->filled('mode_paiement')) {
            $query->where('mode_paiement', $request->input('mode_paiement'));
        }
        
        $paiements = $query->get();
        
        $pdf = PDF::loadView('fournisseurs.paiements.etat-paiements-pdf', [
            'fournisseur' => $fournisseur,
            'paiements' => $paiements,
            'debut' => $request->input('debut'),
            'fin' => $request->input('fin'),
        ]);
        
        return $pdf->download('etat-paiements-fournisseur-' . $fournisseur->id . '.pdf');
    }
}
