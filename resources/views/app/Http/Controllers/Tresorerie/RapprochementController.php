<?php

namespace App\Http\Controllers\Tresorerie;

use App\Http\Controllers\Controller;
use App\Models\Caisse;
use App\Models\Rapprochement;
use App\Models\CompteComptable;
use App\Models\EcritureComptable;
use App\Models\JournalComptable;
use App\Models\LigneEcritureComptable;
use App\Models\MouvementCaisse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RapprochementController extends Controller
{
    /**
     * Affiche la liste des rapprochements effectués (Historique)
     */
    public function index()
    {
        // Récupérer tous les rapprochements avec pagination
        $rapprochements = Rapprochement::with(['caisse', 'utilisateur'])
            ->orderBy('date_rapprochement', 'desc')
            ->paginate(15);

        // Calculer les statistiques pour les widgets du haut de page
        $totalRapprochements = Rapprochement::count();
        
        $rapprochementsMois = Rapprochement::whereYear('date_rapprochement', now()->year)
            ->whereMonth('date_rapprochement', now()->month)
            ->count();
            
        $ecartTotal = Rapprochement::sum('ecart');

        return view('tresorerie.rapprochements.index', compact(
            'rapprochements',
            'totalRapprochements',
            'rapprochementsMois',
            'ecartTotal'
        ));
    }

    /**
     * Affiche le formulaire pour effectuer un nouveau rapprochement
     */
    public function create()
    {
        $caisses = Caisse::where('est_active', true)->get();
        $caisseId = request('caisse_id', $caisses->first()->id ?? null);

        $periodeDebut = request('periode_debut', now()->startOfMonth()->format('Y-m-d'));
        $periodeFin = request('periode_fin', now()->endOfMonth()->format('Y-m-d'));

        $donnees = [];
        $soldeTheorique = 0;
        $soldeReel = 0;
        $ecart = 0;
        $soldeDebutPeriode = 0;

        if ($caisseId) {
            $caisse = Caisse::findOrFail($caisseId);

            // Récupérer le solde théorique (dernier rapprochement validé)
            $dernierRapprochement = Rapprochement::where('caisse_id', $caisseId)
                ->where('statut', 'valide')
                ->where('date_rapprochement', '<=', $periodeDebut) // Le dernier avant la période
                ->orderBy('date_rapprochement', 'desc')
                ->first();

            // Si un rapprochement existe, on part de son solde réel
            // Sinon on part du solde initial de la caisse
            $soldeDebutPeriode = $dernierRapprochement
                ? $dernierRapprochement->solde_reel
                : $caisse->solde_initial;

            // Récupérer les mouvements de la période
            // On exclut les mouvements de type 'rapprochement' s'ils existent dans la table mouvements
            $mouvements = $caisse->mouvements()
                ->whereBetween('date_mouvement', [$periodeDebut, $periodeFin])
                ->where('type_mouvement', '!=', 'rapprochement')
                ->orderBy('date_mouvement')
                ->get();

            $soldeTheorique = $soldeDebutPeriode;

            // Préparer les données pour l'affichage et calculer le solde théorique final
            $donnees = $mouvements->map(function ($mouvement) use (&$soldeTheorique) {
                $soldeAvant = $soldeTheorique;
                $soldeTheorique += $mouvement->montant;

                return [
                    'id' => $mouvement->id,
                    'date' => $mouvement->date_mouvement ? $mouvement->date_mouvement->format('d/m/Y') : '',
                    'reference' => $mouvement->reference_type ?
                        class_basename($mouvement->reference_type) . ' #' . $mouvement->reference_id :
                        'Manuel',
                    'libelle' => $mouvement->description,
                    'debit' => $mouvement->montant > 0 ? number_format($mouvement->montant, 0, ',', ' ') : '',
                    'credit' => $mouvement->montant < 0 ? number_format(abs($mouvement->montant), 0, ',', ' ') : '',
                    'solde' => number_format($soldeTheorique, 0, ',', ' '),
                    'solde_avant' => $soldeAvant,
                    'mouvement' => $mouvement,
                ];
            });

            // Récupérer le solde réel saisi (si rechargement de page) ou par défaut le théorique
            $soldeReel = (float) request('solde_reel', $soldeTheorique);
            $ecart = $soldeReel - $soldeTheorique;
        }

        return view('tresorerie.rapprochements.create', [
            'caisses' => $caisses,
            'caisseId' => $caisseId,
            'periodeDebut' => $periodeDebut,
            'periodeFin' => $periodeFin,
            'donnees' => $donnees,
            'soldeDebutPeriode' => $soldeDebutPeriode,
            'soldeTheorique' => $soldeTheorique,
            'soldeReel' => $soldeReel,
            'ecart' => $ecart,
        ]);
    }

    /**
     * Enregistre un nouveau rapprochement
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'caisse_id' => 'required|exists:caisses,id',
            'date_rapprochement' => 'required|date',
            'solde_theorique' => 'required|numeric',
            'solde_reel' => 'required|numeric',
            'commentaire' => 'nullable|string|max:1000',
            // On pourrait valider les mouvements coches ici si nécessaire
        ]);

        try {
            DB::beginTransaction();

            $caisse = Caisse::findOrFail($validated['caisse_id']);
            $ecart = $validated['solde_reel'] - $validated['solde_theorique'];

            // Créer le rapprochement
            $rapprochement = Rapprochement::create([
                'caisse_id' => $caisse->id,
                'user_id' => auth()->id(),
                'date_rapprochement' => $validated['date_rapprochement'],
                'solde_theorique' => $validated['solde_theorique'],
                'solde_reel' => $validated['solde_reel'],
                'ecart' => $ecart,
                'statut' => 'valide', // Ou 'en_cours' selon votre workflow
                'commentaire' => $validated['commentaire'],
                'reference' => 'RAP-' . date('Ymd') . '-' . rand(100, 999), 
            ]);

            // Mettre à jour le solde actuel de la caisse pour refléter le réel (si on valide)
            $caisse->update([
                'solde_actuel' => $validated['solde_reel'],
                'dernier_rapprochement' => $validated['date_rapprochement'],
            ]);

            // Si on veut marquer les mouvements comme rapprochés, il faudrait récupérer leurs IDs
            if ($request->has('mouvements_ids')) {
                 MouvementCaisse::whereIn('id', $request->input('mouvements_ids'))
                     ->update(['est_rapproche' => true]);
                 
                 // Lier les mouvements au rapprochement
                 $rapprochement->mouvements()->attach($request->input('mouvements_ids'));
            }

            // Si écart, on peut créer un mouvement de régularisation automatiquement
            if (abs($ecart) > 0.01) {
                $caisse->mouvements()->create([
                    'type_mouvement' => 'regularisation', // ou 'rapprochement'
                    'montant' => $ecart,
                    'description' => 'Ecart de rapprochement ' . $rapprochement->reference,
                    'date_mouvement' => $validated['date_rapprochement'],
                    'est_rapproche' => true,
                    'created_by' => auth()->id(),
                    'reference_type' => Rapprochement::class,
                    'reference_id' => $rapprochement->id,
                ]);
            }

            DB::commit();

            return redirect()->route('tresorerie.rapprochements.index')
                ->with('success', 'Le rapprochement a été enregistré avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Erreur lors de l\'enregistrement : ' . $e->getMessage());
        }
    }

    public function valider(Request $request, Caisse $caisse)
    {
        // Méthode conservée pour compatibilité si appelée ailleurs, 
        // mais la logique principale est déplacée dans store()
        return $this->store($request);
    }
    
    // ... Les autres méthodes (genererEcritures, getCompteContrepartie) restent inchangées ...
    public function genererEcritures(Caisse $caisse, $dateDebut, $dateFin)
    {
        // $this->authorize('generate', [EcritureComptable::class, $caisse]);

        try {
            DB::beginTransaction();

            $mouvements = $caisse->mouvements()
                ->whereBetween('date_mouvement', [$dateDebut, $dateFin])
                ->where('type_mouvement', '!=', 'rapprochement')
                ->whereNull('ecriture_comptable_id')
                ->get();

            $compteCaisseNum = $caisse->type === 'principale' ? '571' : '572';
            $compteCaisse = CompteComptable::where('numero', $compteCaisseNum)->firstOrFail();

            $journalCaisse = JournalComptable::where('code', 'CA')->firstOrFail();

            foreach ($mouvements as $mouvement) {
                $dateEcriture = Carbon::parse($mouvement->date_mouvement);
                $libelle = $mouvement->description;
                $montant = abs($mouvement->montant);

                // Créer l'écriture comptable
                $ecriture = EcritureComptable::create([
                    'numero_piece' => 'CA-' . $mouvement->id . '-' . $dateEcriture->format('Ymd'),
                    'date_ecriture' => $dateEcriture,
                    'libelle' => $libelle,
                    'montant_total' => $montant,
                    'validee' => true,
                    'date_validation' => now(),
                    'validateur_id' => auth()->id(),
                    'journal_id' => $journalCaisse->id,
                    'reference_type' => get_class($mouvement),
                    'reference_id' => $mouvement->id,
                ]);

                // Lignes d'écriture
                $compteContrepartie = $this->getCompteContrepartie($mouvement);

                if ($mouvement->montant > 0) {
                    // Entrée en caisse (débit)
                    LigneEcritureComptable::create([
                        'ecriture_comptable_id' => $ecriture->id,
                        'compte_comptable_id' => $compteCaisse->id,
                        'libelle' => $libelle,
                        'montant' => $montant,
                        'sens' => 'debit',
                    ]);

                    LigneEcritureComptable::create([
                        'ecriture_comptable_id' => $ecriture->id,
                        'compte_comptable_id' => $compteContrepartie->id,
                        'libelle' => $libelle,
                        'montant' => $montant,
                        'sens' => 'credit',
                    ]);
                } else {
                    // Sortie de caisse (crédit)
                    LigneEcritureComptable::create([
                        'ecriture_comptable_id' => $ecriture->id,
                        'compte_comptable_id' => $compteCaisse->id,
                        'libelle' => $libelle,
                        'montant' => $montant,
                        'sens' => 'credit',
                    ]);

                    LigneEcritureComptable::create([
                        'ecriture_comptable_id' => $ecriture->id,
                        'compte_comptable_id' => $compteContrepartie->id,
                        'libelle' => $libelle,
                        'montant' => $montant,
                        'sens' => 'debit',
                    ]);
                }

                // Lier l'écriture au mouvement
                $mouvement->ecriture_comptable_id = $ecriture->id;
                $mouvement->save();
            }

            DB::commit();

            return back()
                ->with('success', count($mouvements) . ' écritures comptables ont été générées avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'Une erreur est survenue lors de la génération des écritures : ' . $e->getMessage());
        }
    }

    protected function getCompteContrepartie(MouvementCaisse $mouvement)
    {
        $compte = null;

        switch ($mouvement->type_mouvement) {
            case 'approvisionnement':
                $compte = $mouvement->montant > 0
                    ? CompteComptable::where('numero', '571')->first()
                    : CompteComptable::where('numero', '572')->first();
                break;

            case 'depense':
                $compte = CompteComptable::where('numero', '606000')->first();
                break;

            default:
                $compte = CompteComptable::where('numero', '471000')->first();
                break;
        }

        return $compte ?? CompteComptable::where('numero', '471000')->firstOrFail();
    }
}
