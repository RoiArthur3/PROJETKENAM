<?php

namespace App\Http\Controllers;

use App\Models\Caisse;
use App\Models\CommandeFournisseur;
use App\Models\CompteComptable;
use App\Models\DepenseCaisse;
use App\Models\EcritureComptable;
use App\Models\Expense;
use App\Models\FactureFournisseur;
use App\Models\Fournisseur;
use App\Models\LigneCommandeFournisseur;
use App\Models\LigneFactureFournisseur;
use App\Models\MouvementCaisse;
use App\Models\PaiementFournisseur;
use App\Models\Produit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AchatController extends Controller
{
    public function index(Request $request): View
    {
        $query = CommandeFournisseur::query()
            ->with(['fournisseur', 'lignes', 'expense.depenseCaisse', 'compteComptable'])
            ->orderByDesc('date_commande');

        if ($request->filled('statut')) {
            $query->where('statut', $request->string('statut')->toString());
        }

        if ($request->filled('type_achat')) {
            $query->where('type_achat', $request->string('type_achat')->toString());
        }

        if ($request->filled('fournisseur_id')) {
            $query->where('fournisseur_id', (int) $request->input('fournisseur_id'));
        }

        if ($request->filled('recherche')) {
            $term = trim((string) $request->input('recherche'));
            $query->where(function ($subQuery) use ($term): void {
                $subQuery->where('reference', 'like', '%' . $term . '%')
                    ->orWhere('service_concerne', 'like', '%' . $term . '%')
                    ->orWhere('notes', 'like', '%' . $term . '%')
                    ->orWhereHas('fournisseur', function ($fournisseurQuery) use ($term): void {
                        $fournisseurQuery->where('raison_sociale', 'like', '%' . $term . '%');
                    })
                    ->orWhereHas('lignes', function ($ligneQuery) use ($term): void {
                        $ligneQuery->where('designation', 'like', '%' . $term . '%')
                            ->orWhere('description', 'like', '%' . $term . '%');
                    });
            });
        }

        $achats = $query->paginate(15)->withQueryString();
        $fournisseurs = Fournisseur::orderBy('raison_sociale')->get(['id', 'raison_sociale']);

        $statsCollection = (clone $query)->get();
        $stats = [
            'total' => $statsCollection->count(),
            'montant_total' => (float) $statsCollection->sum('montant_ttc'),
            'valides' => $statsCollection->where('expense_id', '!=', null)->count(),
            'a_decaisser' => $statsCollection->filter(fn (CommandeFournisseur $commande) => $commande->expense && !$commande->expense->depenseCaisse)->count(),
            'decaisses' => $statsCollection->filter(fn (CommandeFournisseur $commande) => $commande->expense && $commande->expense->depenseCaisse)->count(),
        ];

        return view('achat.index', compact('achats', 'fournisseurs', 'stats'));
    }

    public function create(): View
    {
        $fournisseurs = Fournisseur::orderBy('raison_sociale')->get();
        $articles = collect();
        if (Schema::hasTable('produits')) {
            $articles = Produit::query()->orderBy('designation')->get();
        }

        $comptes = CompteComptable::query()->orderByRaw('COALESCE(numero_compte, intitule, id) asc')->get();

        return view('achat.create', compact('fournisseurs', 'articles', 'comptes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'reference' => 'nullable|string|max:50|unique:commande_fournisseurs,reference',
            'type_achat' => 'required|string|max:100',
            'service_concerne' => 'nullable|string|max:255',
            'compte_comptable_id' => 'required|exists:comptes_comptables,id',
            'date_commande' => 'required|date',
            'date_livraison_prevue' => 'nullable|date|after_or_equal:date_commande',
            'mode_paiement' => 'nullable|string|max:50',
            'conditions_paiement' => 'nullable|string|max:255',
            'frais_livraison' => 'nullable|numeric|min:0',
            'remise' => 'nullable|numeric|min:0',
            'type_remise' => 'required_with:remise|in:pourcentage,montant',
            'notes' => 'nullable|string',
            'lignes' => 'required|array|min:1',
            'lignes.*.article_id' => 'nullable|exists:produits,id',
            'lignes.*.reference_article' => 'nullable|string|max:100',
            'lignes.*.designation' => 'required|string|max:255',
            'lignes.*.description' => 'nullable|string',
            'lignes.*.quantite' => 'required|numeric|min:0.001',
            'lignes.*.unite' => 'required|string|max:20',
            'lignes.*.prix_unitaire_ht' => 'required|numeric|min:0',
            'lignes.*.tva_taux' => 'required|numeric|min:0|max:100',
            'lignes.*.remise' => 'nullable|numeric|min:0|max:100',
            'lignes.*.notes' => 'nullable|string',
        ]);

        $fournisseur = Fournisseur::findOrFail($validated['fournisseur_id']);
        $totaux = $this->calculerTotaux(
            $validated['lignes'],
            (float) ($validated['frais_livraison'] ?? 0),
            (float) ($validated['remise'] ?? 0),
            (string) ($validated['type_remise'] ?? 'pourcentage')
        );

        DB::beginTransaction();

        try {
            $achat = CommandeFournisseur::create([
                'reference' => $validated['reference'] ?? 'ACH-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
                'fournisseur_id' => $fournisseur->id,
                'type_achat' => $validated['type_achat'],
                'service_concerne' => $validated['service_concerne'] ?? null,
                'compte_comptable_id' => $validated['compte_comptable_id'],
                'date_commande' => $validated['date_commande'],
                'date_livraison_prevue' => $validated['date_livraison_prevue'] ?? null,
                'mode_paiement' => $validated['mode_paiement'] ?? null,
                'conditions_paiement' => $validated['conditions_paiement'] ?? null,
                'frais_livraison' => $validated['frais_livraison'] ?? 0,
                'remise' => $validated['remise'] ?? 0,
                'type_remise' => $validated['type_remise'] ?? 'pourcentage',
                'montant_ht' => $totaux['montant_ht'],
                'tva' => $totaux['tva'],
                'montant_ttc' => $totaux['montant_ttc'],
                'statut' => 'brouillon',
                'notes' => $validated['notes'] ?? null,
                'user_id' => Auth::id(),
            ]);

            foreach ($validated['lignes'] as $ligne) {
                LigneCommandeFournisseur::create([
                    'commande_id' => $achat->id,
                    'article_id' => $ligne['article_id'] ?? null,
                    'reference_article' => $ligne['reference_article'] ?? null,
                    'designation' => $ligne['designation'],
                    'description' => $ligne['description'] ?? null,
                    'quantite' => $ligne['quantite'],
                    'unite' => $ligne['unite'],
                    'prix_unitaire_ht' => $ligne['prix_unitaire_ht'],
                    'tva_taux' => $ligne['tva_taux'],
                    'remise' => $ligne['remise'] ?? 0,
                    'montant_ht' => $ligne['quantite'] * $ligne['prix_unitaire_ht'] * (1 - ($ligne['remise'] ?? 0) / 100),
                    'montant_tva' => $ligne['quantite'] * $ligne['prix_unitaire_ht'] * ($ligne['tva_taux'] / 100) * (1 - ($ligne['remise'] ?? 0) / 100),
                    'montant_ttc' => $ligne['quantite'] * $ligne['prix_unitaire_ht'] * (1 + ($ligne['tva_taux'] / 100)) * (1 - ($ligne['remise'] ?? 0) / 100),
                    'date_livraison_prevue' => $validated['date_livraison_prevue'] ?? null,
                    'statut' => 'en_attente',
                    'notes' => $ligne['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('achat.show', $achat)->with('success', 'Achat enregistré avec succès.');
        } catch (\Throwable $throwable) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Impossible d\'enregistrer l\'achat : ' . $throwable->getMessage());
        }
    }

    public function show(CommandeFournisseur $commande): View
    {
        $commande->load([
            'fournisseur',
            'lignes',
            'expense.depenseCaisse',
            'compteComptable',
            'validatedBy',
            'factures.paiements.depenseCaisse',
        ]);

        $caisses = Caisse::query()
            ->where('est_active', true)
            ->orderBy('nom')
            ->get(['id', 'nom', 'solde_actuel']);

        return view('achat.show', [
            'achat' => $commande,
            'caisses' => $caisses,
        ]);
    }

    public function storeFacture(Request $request, CommandeFournisseur $commande): RedirectResponse
    {
        $commande->loadMissing(['fournisseur', 'lignes', 'factures']);

        $validated = $request->validate([
            'numero_facture' => 'required|string|max:100|unique:facture_fournisseurs,numero_facture',
            'reference' => 'nullable|string|max:50|unique:facture_fournisseurs,reference',
            'date_facture' => 'required|date',
            'date_echeance' => 'required|date|after_or_equal:date_facture',
            'conditions_paiement' => 'required|string|max:100',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $facture = FactureFournisseur::create([
                'reference' => $validated['reference'] ?? 'FAC-ACH-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5)),
                'fournisseur_id' => $commande->fournisseur_id,
                'commande_id' => $commande->id,
                'numero_facture' => $validated['numero_facture'],
                'date_facture' => $validated['date_facture'],
                'date_echeance' => $validated['date_echeance'],
                'conditions_paiement' => $validated['conditions_paiement'],
                'frais_livraison' => $commande->frais_livraison ?? 0,
                'remise' => $commande->remise ?? 0,
                'type_remise' => $commande->type_remise ?? 'pourcentage',
                'montant_ht' => $commande->montant_ht ?? 0,
                'tva' => $commande->tva ?? 0,
                'montant_ttc' => $commande->montant_ttc ?? 0,
                'montant_paye' => 0,
                'reste_a_payer' => $commande->montant_ttc ?? 0,
                'statut' => 'non_payee',
                'notes' => $validated['notes'] ?? null,
                'user_id' => Auth::id(),
            ]);

            foreach ($commande->lignes as $ligne) {
                LigneFactureFournisseur::create([
                    'facture_id' => $facture->id,
                    'ligne_commande_id' => $ligne->id,
                    'quantite' => $ligne->quantite,
                    'prix_unitaire_ht' => $ligne->prix_unitaire_ht,
                    'tva_taux' => $ligne->tva_taux,
                    'remise' => $ligne->remise ?? 0,
                    'montant_ht' => $ligne->montant_ht,
                    'montant_tva' => $ligne->montant_tva,
                    'montant_ttc' => $ligne->montant_ttc,
                    'description' => $ligne->description,
                ]);
            }

            DB::commit();

            return redirect()->route('achat.show', $commande)->with('success', 'Facture fournisseur créée avec succès.');
        } catch (\Throwable $throwable) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Impossible de créer la facture : ' . $throwable->getMessage());
        }
    }

    public function storePaiement(Request $request, CommandeFournisseur $commande, FactureFournisseur $facture): RedirectResponse
    {
        if ((int) $facture->commande_id !== (int) $commande->id) {
            abort(404);
        }

        $validated = $request->validate([
            'date_paiement' => 'required|date',
            'montant' => 'required|numeric|min:0.01|max:' . max(0, (float) $facture->reste_a_payer),
            'mode_paiement' => 'required|string|max:50',
            'reference_paiement' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'caisse_id' => 'nullable|exists:caisses,id',
        ]);

        DB::beginTransaction();

        try {
            $paiement = PaiementFournisseur::create([
                'reference' => 'PAY-ACH-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5)),
                'fournisseur_id' => $commande->fournisseur_id,
                'facture_id' => $facture->id,
                'date_paiement' => $validated['date_paiement'],
                'montant' => $validated['montant'],
                'mode_paiement' => $validated['mode_paiement'],
                'reference_paiement' => $validated['reference_paiement'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'user_id' => Auth::id(),
            ]);

            $montantPaye = round((float) $facture->montant_paye + (float) $validated['montant'], 2);
            $resteAPayer = round(max(0, (float) $facture->montant_ttc - $montantPaye), 2);

            $facture->forceFill([
                'montant_paye' => $montantPaye,
                'reste_a_payer' => $resteAPayer,
            ]);
            $facture->statut = $facture->reste_a_payer <= 0 ? 'payee' : 'partiellement_payee';
            if ($facture->reste_a_payer <= 0) {
                $facture->date_paiement = $validated['date_paiement'];
            }
            $facture->save();

            if (!empty($validated['caisse_id'])) {
                $this->createCashDecaissementForPaiement($commande, $facture, $paiement, $validated);
            }

            DB::commit();

            return redirect()->route('achat.show', $commande)->with('success', 'Paiement fournisseur enregistré avec succès.');
        } catch (\Throwable $throwable) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Impossible d\'enregistrer le paiement : ' . $throwable->getMessage());
        }
    }

    public function valider(CommandeFournisseur $commande): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $expense = $commande->expense;
            $designation = $commande->lignes->first()?->designation ?? ('Achat ' . $commande->reference);
            $description = trim(collect([
                'Achat ' . $commande->reference,
                'Type: ' . ($commande->type_achat ?? 'N/A'),
                'Fournisseur: ' . ($commande->fournisseur->raison_sociale ?? 'N/A'),
                $commande->notes,
            ])->filter()->implode(' | '));

            $expensePayload = [
                'reference' => 'EXP-' . $commande->reference,
                'intitule' => $designation,
                'categorie' => $commande->type_achat ?? 'Achat',
                'description' => $description,
                'montant' => $commande->montant_ttc,
                'date_depense' => $commande->date_commande,
                'user_id' => $commande->user_id ?? Auth::id(),
                'fournisseur' => $commande->fournisseur->raison_sociale ?? null,
                'statut' => 'approuvee',
                'mode_paiement' => $this->normalizeExpensePaymentMode($commande->mode_paiement),
                'service_concerne' => $commande->service_concerne ?? 'Achat',
                'approuve_par' => Auth::id(),
                'date_approbation' => now(),
            ];

            if ($expense) {
                $expense->update($expensePayload);
            } else {
                $expense = Expense::create($expensePayload);
            }

            $commande->update([
                'statut' => 'validee',
                'expense_id' => $expense->id,
                'validated_by' => Auth::id(),
                'date_validation_achat' => now(),
            ]);

            DB::commit();

            return redirect()->route('achat.show', $commande)->with('success', 'Achat validé et transmis en comptabilité.');
        } catch (\Throwable $throwable) {
            DB::rollBack();

            return back()->with('error', 'Impossible de valider l\'achat : ' . $throwable->getMessage());
        }
    }

    public function redirectToDecaissement(CommandeFournisseur $commande): RedirectResponse
    {
        $commande->loadMissing('expense');

        if (!$commande->expense) {
            return back()->with('error', 'Validez d\'abord l\'achat pour générer la dépense comptable.');
        }

        if ($commande->expense->depenseCaisse) {
            return redirect()->route('tresorerie.depenses.show', $commande->expense->depenseCaisse)->with('success', 'Cet achat est déjà décaissé.');
        }

        return redirect()->route('tresorerie.depenses.create', [
            'expense_id' => $commande->expense_id,
            'compte_comptable_id' => $commande->compte_comptable_id,
        ]);
    }

    private function calculerTotaux(array $lignes, float $fraisLivraison, float $remise, string $typeRemise): array
    {
        $montantHT = 0;
        $tva = 0;

        foreach ($lignes as $ligne) {
            $quantite = (float) $ligne['quantite'];
            $prixUnitaire = (float) $ligne['prix_unitaire_ht'];
            $tauxTva = (float) $ligne['tva_taux'];
            $remiseLigne = (float) ($ligne['remise'] ?? 0);

            $totalLigneHT = $quantite * $prixUnitaire * (1 - $remiseLigne / 100);
            $montantHT += $totalLigneHT;
            $tva += $totalLigneHT * ($tauxTva / 100);
        }

        $montantHT += $fraisLivraison;
        if ($typeRemise === 'pourcentage') {
            $montantHT -= $montantHT * ($remise / 100);
        } else {
            $montantHT -= $remise;
        }

        $montantTTC = $montantHT + $tva;

        return [
            'montant_ht' => round(max($montantHT, 0), 2),
            'tva' => round(max($tva, 0), 2),
            'montant_ttc' => round(max($montantTTC, 0), 2),
        ];
    }

    private function normalizeExpensePaymentMode(?string $modePaiement): string
    {
        return match ($modePaiement) {
            'espece', 'especes' => 'espece',
            'cheque' => 'cheque',
            'carte', 'carte_bancaire' => 'carte_bancaire',
            default => 'virement',
        };
    }

    private function createCashDecaissementForPaiement(
        CommandeFournisseur $commande,
        FactureFournisseur $facture,
        PaiementFournisseur $paiement,
        array $validated
    ): void {
        $caisse = Caisse::query()->lockForUpdate()->findOrFail($validated['caisse_id']);

        $depense = DepenseCaisse::create([
            'reference' => 'DEC-ACH-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5)),
            'fournisseur_id' => $commande->fournisseur_id,
            'caisse_id' => $caisse->id,
            'compte_comptable_id' => $commande->compte_comptable_id,
            'libelle' => 'Paiement achat ' . $commande->reference,
            'description' => $validated['notes'] ?? ('Paiement partiel facture fournisseur ' . $facture->numero_facture),
            'montant' => $validated['montant'],
            'devise' => 'XOF',
            'date_depense' => $validated['date_paiement'],
            'mode_paiement' => $validated['mode_paiement'],
            'statut' => 'paye',
            'created_by' => Auth::id(),
            'facture_fournisseur_id' => $facture->id,
            'paiement_fournisseur_id' => $paiement->id,
        ]);

        $caisse->mouvements()->create(MouvementCaisse::normalizePayload([
            'type_mouvement' => MouvementCaisse::TYPE_DEPENSE,
            'montant' => -$depense->montant,
            'libelle' => 'Paiement fournisseur',
            'description' => 'Paiement facture ' . ($facture->numero_facture ?? $facture->reference),
            'created_by' => Auth::id(),
            'devise' => $caisse->devise ?? 'XOF',
        ], $depense));

        $caisse->solde_actuel = (float) $caisse->solde_actuel - (float) $depense->montant;
        $caisse->save();

        if (Schema::hasTable('ecritures_comptables')) {
            EcritureComptable::create([
                'date' => $depense->date_depense,
                'reference' => $depense->reference,
                'piece_comptable' => $paiement->reference_paiement,
                'libelle' => $depense->libelle,
                'compte_debit' => $commande->compteComptable?->numero
                    ?? $commande->compteComptable?->numero_compte
                    ?? $commande->compteComptable?->code
                    ?? (string) ($commande->compte_comptable_id ?? '6'),
                'compte_credit' => (string) ($caisse->code ?? '57'),
                'montant' => $depense->montant,
                'description' => $depense->description,
                'source_type' => PaiementFournisseur::class,
                'source_id' => $paiement->id,
                'created_by' => Auth::id(),
            ]);
        }
    }
}
