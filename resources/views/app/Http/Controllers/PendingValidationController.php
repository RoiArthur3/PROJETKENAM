<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Operation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PendingValidationController extends Controller
{
    /**
     * Filtre les opérations selon le rôle de l'utilisateur.
     * Admin/superadmin voient tout.
     * Agents voient : leurs propres opérations + celles où ils sont validateurs assignés.
     */
    private function applyUserFilter($query, $user)
    {
        if ($user && !in_array($user->role, ['admin', 'superadmin'])) {
            $query->where(function($q) use ($user) {
                $q->where('operations.user_id', $user->id)
                  ->orWhereIn('operations.id', function($sub) use ($user) {
                      $sub->select('osv.operation_id')
                          ->from('operation_service_validation as osv')
                          ->join('services_operationnels as so', 'osv.service_operationnel_id', '=', 'so.id')
                          ->where(function($w) use ($user) {
                              $w->where('so.email', $user->email);
                              if ($user->service_id) {
                                  $w->orWhere('osv.service_operationnel_id', $user->service_id);
                              }
                          });
                  });
            });
        }
        return $query;
    }

    /**
     * Validations en attente (statut_courant = en_attente, pending_validation, en_validation)
     * Exclut explicitement les opérations approuvées ou rejetées.
     */
    public function pending()
    {
        $user = Auth::user();
        $query = Operation::with(['initiateur', 'services'])
            ->whereIn('statut_courant', ['en_attente', 'pending_validation', 'en_validation'])
            ->whereNotIn('statut_courant', ['approuvee', 'approuve', 'rejetee', 'terminee']);

        $query = $this->applyUserFilter($query, $user);

        $operations = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('validations.pending', compact('operations'));
    }

    /**
     * Validations approuvées (statut_courant = approuvee ou approuve)
     */
    public function approved()
    {
        $user = Auth::user();
        $query = Operation::with(['initiateur', 'services'])
            ->whereIn('statut_courant', ['approuvee', 'approuve']);

        $query = $this->applyUserFilter($query, $user);

        $operations = $query->orderBy('updated_at', 'desc')->paginate(20);

        return view('validations.approved', compact('operations'));
    }

    /**
     * Validations rejetées (statut_courant = rejetee)
     */
    public function rejected()
    {
        $user = Auth::user();
        $query = Operation::with(['initiateur', 'services'])
            ->where('statut_courant', 'rejetee');

        $query = $this->applyUserFilter($query, $user);

        $operations = $query->orderBy('updated_at', 'desc')->paginate(20);

        return view('validations.rejected', compact('operations'));
    }

    /**
     * Historique de toutes les validations
     */
    public function history()
    {
        $user = Auth::user();
        $query = Operation::with(['initiateur', 'services']);

        $query = $this->applyUserFilter($query, $user);

        $operations = $query->orderBy('updated_at', 'desc')->paginate(20);

        return view('validations.history', ['validations' => $operations, 'operations' => $operations]);
    }

    public function show($id)
    {
        $validation = Operation::with(['initiateur', 'services'])->findOrFail($id);
        return view('validations.show', compact('validation'));
    }
}
