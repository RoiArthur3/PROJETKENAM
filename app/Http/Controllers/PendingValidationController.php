<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Operation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PendingValidationController extends Controller
{
    private function ensureValidationAccess(): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user || !$user->canAccessModule('validations')) {
            abort(403, 'Accès non autorisé au module validations');
        }
    }

    /**
     * Filtre les opérations selon le rôle de l'utilisateur.
     * Admin/superadmin voient tout.
     * Agents voient uniquement leurs propres opérations.
     */
    private function applyUserFilter($query, $user)
    {
        if (!$user || in_array($user->role, ['admin', 'superadmin'])) {
            return $query;
        }

        if (in_array($user->role, ['agent', 'user'], true)) {
            return $query->where('operations.user_id', $user->id);
        }

        // Filtre complexe pour les autres rôles
        $filtered = false;
        if ($user) {
            $query->where(function($q) use ($user, &$filtered) {
                $q->where('operations.user_id', $user->id);
                $filtered = true;
                $q->orWhereIn('operations.id', function($sub) use ($user, &$filtered) {
                    $sub->select('osv.operation_id')
                        ->from('operation_service_validation as osv')
                        ->join('services_operationnels as so', 'osv.service_operationnel_id', '=', 'so.id')
                        ->where(function($w) use ($user, &$filtered) {
                            if ($user->email) {
                                $w->where('so.email', $user->email);
                                $filtered = true;
                            }
                            if ($user->service_id) {
                                $w->orWhere('osv.service_operationnel_id', $user->service_id);
                                $filtered = true;
                            }
                        });
                });
            });
        }
        // Si aucun critère n'a filtré, retourner toutes les opérations (ne pas filtrer)
        if (!$filtered) {
            return $query;
        }
        return $query;
    }

    /**
     * Validations en attente (étapes EN_COURS ou EN_ATTENTE)
     * Exclut explicitement les opérations approuvées ou rejetées.
     */
    public function pending()
    {
        $this->ensureValidationAccess();
        /** @var User|null $user */
        $user = Auth::user();
        
        // Récupérer les opérations avec des étapes en attente ou en cours de validation
        $query = Operation::with(['initiateur', 'services', 'client', 'typeOperation', 'operationalService'])
            ->whereExists(function($q) {
                $q->select(DB::raw(1))
                  ->from('operation_service_validation')
                  ->whereColumn('operation_service_validation.operation_id', 'operations.id')
                  ->whereIn('operation_service_validation.statut', ['EN_ATTENTE', 'EN_COURS']);
            })
            ->where('statut_courant', '!=', 'rejetee');

        // Application des filtres avancés
        $montant_min = request('montant_min');
        $montant_max = request('montant_max');
        $date_debut = request('date_debut');
        $date_fin = request('date_fin');

        if ($montant_min !== null && $montant_min !== '') {
            $query->where('montant', '>=', $montant_min);
        }
        if ($montant_max !== null && $montant_max !== '') {
            $query->where('montant', '<=', $montant_max);
        }
        if ($date_debut !== null && $date_debut !== '') {
            $query->whereDate('created_at', '>=', $date_debut);
        }
        if ($date_fin !== null && $date_fin !== '') {
            $query->whereDate('created_at', '<=', $date_fin);
        }

        $query = $this->applyUserFilter($query, $user);

        $operations = $query->distinct()
                          ->orderBy('operations.created_at', 'desc')
                          ->paginate(20);

        return view('validations.pending', compact('operations'));
    }

    /**
     * Validations approuvées (toutes les étapes APPROUVÉES ou statuts approuvés)
     */
    public function approved()
    {
        $this->ensureValidationAccess();
        /** @var User|null $user */
        $user = Auth::user();
        
        // Statuts approuvés
        $approvedStatus = [
            'approuvee', 'approuve', 'Approuvé_en_attente_paiement', 
            'bon_pour_accord', 'pret_execution', 'payee', 'payée'
        ];
        
        $query = Operation::with(['initiateur', 'services', 'client', 'typeOperation', 'operationalService'])
            ->where(function($q) use ($approvedStatus) {
                // Cas 1 : Statut approuvé
                $q->whereIn('statut_courant', $approvedStatus)
                  // Cas 2 : Toutes les étapes de validation sont approuvées
                  ->orWhereExists(function($subQ) {
                      $subQ->select(DB::raw(1))
                           ->from('operation_service_validation')
                           ->whereColumn('operation_service_validation.operation_id', 'operations.id')
                           ->groupBy('operation_service_validation.operation_id')
                           ->havingRaw('COUNT(CASE WHEN operation_service_validation.statut != ? THEN 1 END) = 0', ['APPROUVE']);
                  });
            });

        $query = $this->applyUserFilter($query, $user);

        $operations = $query->distinct()
                           ->orderBy('operations.updated_at', 'desc')
                           ->paginate(20);

        return view('validations.approved', compact('operations'));
    }

    /**
     * Validations rejetées (statut_courant = rejetee)
     */
    public function rejected()
    {
        $this->ensureValidationAccess();
        /** @var User|null $user */
        $user = Auth::user();
        $query = Operation::with(['initiateur', 'services', 'client', 'typeOperation', 'operationalService'])
            ->where(function($q) {
                // Opérations avec statut rejeté OU avec au moins une étape rejetée
                $q->where('statut_courant', 'rejetee')
                  ->orWhereExists(function($subQ) {
                      $subQ->select(DB::raw(1))
                           ->from('operation_service_validation')
                           ->whereColumn('operation_service_validation.operation_id', 'operations.id')
                           ->where('operation_service_validation.statut', 'REJETE');
                  });
            });

        $query = $this->applyUserFilter($query, $user);

        $operations = $query->distinct()
                           ->orderBy('operations.updated_at', 'desc')
                           ->paginate(20);

        return view('validations.rejected', compact('operations'));
    }

    /**
     * Historique de toutes les validations
     */
    public function history()
    {
        $this->ensureValidationAccess();
        /** @var User|null $user */
        $user = Auth::user();
        $query = Operation::with(['initiateur', 'services']);

        $query = $this->applyUserFilter($query, $user);

        $operations = $query->orderBy('updated_at', 'desc')->paginate(20);

        return view('validations.history', ['validations' => $operations, 'operations' => $operations]);
    }

    public function show($id)
    {
        $this->ensureValidationAccess();

        /** @var User|null $user */
        $user = Auth::user();
        $query = Operation::with(['initiateur', 'services'])->whereKey($id);
        $query = $this->applyUserFilter($query, $user);

        $validation = $query->firstOrFail();

        return view('validations.show', compact('validation'));
    }
}
