<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Facture;
use App\Models\Client;
use App\Models\LigneFacture;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FactureController extends Controller
{
    /**
     * Afficher la liste des factures
     */
    public function index(Request $request): View
    {
        try {
            $query = Facture::with(['client', 'lignes']);

            // Filtres
            if ($request->filled('statut')) {
                $query->where('statut', $request->statut);
            }

            if ($request->filled('client_id')) {
                $query->where('client_id', $request->client_id);
            }

            if ($request->filled('date_debut')) {
                $query->whereDate('date_facture', '>=', $request->date_debut);
            }

            if ($request->filled('date_fin')) {
                $query->whereDate('date_facture', '<=', $request->date_fin);
            }

            // Recherche
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('numero', 'like', "%{$search}%")
                      ->orWhere('reference', 'like', "%{$search}%")
                      ->orWhereHas('client', function($subQuery) use ($search) {
                          $subQuery->where('nom', 'like', "%{$search}%");
                      });
                });
            }

            $factures = $query->orderBy('date_facture', 'desc')->paginate(25);

            // Statistiques
            $stats = [
                'total' => Facture::count(),
                'payees' => Facture::where('statut', 'payee')->count(),
                'impayees' => Facture::where('statut', 'impayee')->count(),
                'en_attente' => Facture::where('statut', 'en_attente')->count(),
                'montant_total' => Facture::sum('montant_total'),
                'montant_paye' => Facture::where('statut', 'payee')->sum('montant_total'),
                'montant_impaye' => Facture::where('statut', 'impayee')->sum('montant_total'),
            ];

            $clients = Client::orderBy('nom')->get();

            return view('facturation.index', compact('factures', 'stats', 'clients'));
        } catch (\Exception $e) {
            // En cas d'erreur, retourner des données vides
            $factures = collect([]);
            $stats = [
                'total' => 0, 'payees' => 0, 'impayees' => 0, 'en_attente' => 0,
                'montant_total' => 0, 'montant_paye' => 0, 'montant_impaye' => 0
            ];
            $clients = collect([]);
            return view('facturation.index', compact('factures', 'stats', 'clients'));
        }
    }

    /**
     * Afficher le formulaire de création
     */
    public function create(): View
    {
        try {
            $clients = Client::orderBy('nom')->get();
            $lastFacture = Facture::orderBy('created_at', 'desc')->first();
            $nextNumero = $lastFacture ? $lastFacture->numero + 1 : 1;

            return view('facturation.create', compact('clients', 'nextNumero'));
        } catch (\Exception $e) {
            $clients = collect([]);
            $nextNumero = 1;
            return view('facturation.create', compact('clients', 'nextNumero'));
        }
    }

    /**
     * Enregistrer une nouvelle facture
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'client_id' => 'required|exists:clients,id',
                'date_facture' => 'required|date',
                'date_echeance' => 'required|date|after_or_equal:date_facture',
                'reference' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'lignes' => 'required|array|min:1',
                'lignes.*.description' => 'required|string|max:255',
                'lignes.*.quantite' => 'required|numeric|min:0',
                'lignes.*.prix_unitaire' => 'required|numeric|min:0',
            ]);

            DB::beginTransaction();

            // Créer la facture
            $facture = Facture::create([
                'client_id' => $validated['client_id'],
                'numero' => Facture::max('numero') + 1,
                'date_facture' => $validated['date_facture'],
                'date_echeance' => $validated['date_echeance'],
                'reference' => $validated['reference'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'statut' => 'en_attente',
                'montant_total' => 0,
            ]);

            // Ajouter les lignes
            $montantTotal = 0;
            foreach ($validated['lignes'] as $ligne) {
                $montantLigne = $ligne['quantite'] * $ligne['prix_unitaire'];
                $montantTotal += $montantLigne;

                LigneFacture::create([
                    'facture_id' => $facture->id,
                    'description' => $ligne['description'],
                    'quantite' => $ligne['quantite'],
                    'prix_unitaire' => $ligne['prix_unitaire'],
                    'montant_total' => $montantLigne,
                ]);
            }

            // Mettre à jour le montant total de la facture
            $facture->update(['montant_total' => $montantTotal]);

            DB::commit();

            return redirect()->route('facturation.index')
                ->with('success', "Facture #{$facture->numero} créée avec succès.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de la facture: ' . $e->getMessage());
        }
    }

    /**
     * Afficher une facture
     */
    public function show($facture): View
    {
        try {
            $facture = Facture::with(['client', 'lignes'])->findOrFail($facture);
            return view('facturation.show', compact('facture'));
        } catch (\Exception $e) {
            return redirect()->route('facturation.index')
                ->with('error', 'Facture introuvable.');
        }
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit($facture): View
    {
        try {
            $facture = Facture::with(['client', 'lignes'])->findOrFail($facture);
            $clients = Client::orderBy('nom')->get();

            return view('facturation.edit', compact('facture', 'clients'));
        } catch (\Exception $e) {
            return redirect()->route('facturation.index')
                ->with('error', 'Facture introuvable.');
        }
    }

    /**
     * Mettre à jour une facture
     */
    public function update(Request $request, $facture): RedirectResponse
    {
        try {
            $facture = Facture::findOrFail($facture);

            $validated = $request->validate([
                'client_id' => 'required|exists:clients,id',
                'date_facture' => 'required|date',
                'date_echeance' => 'required|date|after_or_equal:date_facture',
                'reference' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'statut' => 'required|in:en_attente,partiellement_payee,payee,impayee,annulee',
            ]);

            $facture->update($validated);

            return redirect()->route('facturation.index')
                ->with('success', "Facture #{$facture->numero} mise à jour avec succès.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer une facture
     */
    public function destroy($facture): RedirectResponse
    {
        try {
            $facture = Facture::findOrFail($facture);

            // Vérifier si la facture peut être supprimée
            if ($facture->statut === 'payee') {
                return redirect()->route('facturation.index')
                    ->with('error', 'Impossible de supprimer une facture déjà payée.');
            }

            DB::beginTransaction();

            // Supprimer les lignes
            $facture->lignes()->delete();

            // Supprimer la facture
            $facture->delete();

            DB::commit();

            return redirect()->route('facturation.index')
                ->with('success', "Facture #{$facture->numero} supprimée avec succès.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('facturation.index')
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Générer le PDF d'une facture
     */
    public function generatePdf($facture)
    {
        try {
            $facture = Facture::with(['client', 'lignes'])->findOrFail($facture);

            // TODO: Implémenter la génération PDF avec DomPDF ou autre librairie
            return redirect()->back()
                ->with('info', 'Génération PDF en cours de développement.');
        } catch (\Exception $e) {
            return redirect()->route('facturation.index')
                ->with('error', 'Erreur lors de la génération du PDF.');
        }
    }
}
