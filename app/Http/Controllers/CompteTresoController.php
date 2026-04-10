<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CompteTresoController extends Controller
{
    /**
     * Vérifier que l'utilisateur est bien un comptetreso
     */
    private function checkCompteTresoAccess()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'comptetreso') {
            Log::warning('Accès non autorisé au Compte Treso', [
                'user_id' => $user->id ?? null,
                'role' => $user->role ?? null,
                'ip' => request()->ip()
            ]);
            abort(403, 'Accès réservé au Compte Treso');
        }
        return $user;
    }

    /**
     * Dashboard du Compte Treso
     */
    public function dashboard()
    {
        $this->checkCompteTresoAccess();

        try {
            // Uniquement les opérations approuvées ou à payer
            $operationsToPay = Operation::whereIn('statut_courant', ['approved', 'a_payer'])
                ->with(['service', 'typeOperation'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // Statistiques sécurisées
            $stats = [
                'total_to_pay' => Operation::whereIn('statut_courant', ['approved', 'a_payer'])->sum('montant'),
                'count_approved' => Operation::where('statut_courant', 'approved')->count(),
                'count_to_pay' => Operation::where('statut_courant', 'a_payer')->count(),
                'count_paid_today' => Operation::where('statut_courant', 'paid')
                    ->whereDate('date_paiement', today())->count(),
            ];

            return view('comptetreso.dashboard', compact('operationsToPay', 'stats'));

        } catch (\Exception $e) {
            Log::error('Erreur dashboard Compte Treso', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return back()->with('error', 'Erreur lors du chargement du dashboard');
        }
    }

    /**
     * Liste des opérations à payer
     */
    public function operationsToPay()
    {
        $this->checkCompteTresoAccess();

        try {
            $operations = Operation::whereIn('statut_courant', ['approved', 'a_payer'])
                ->with(['service', 'typeOperation', 'user'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return view('comptetreso.operations', compact('operations'));

        } catch (\Exception $e) {
            Log::error('Erreur liste opérations Compte Treso', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return back()->with('error', 'Erreur lors du chargement des opérations');
        }
    }

    /**
     * Marquer une opération comme payée
     */
    public function markAsPaid($id)
    {
        $this->checkCompteTresoAccess();

        try {
            $operation = Operation::findOrFail($id);

            // Vérifier que l'opération est approuvée ou à payer
            if (!in_array($operation->statut_courant, ['approved', 'a_payer'])) {
                return back()->with('error', 'Cette opération ne peut pas être marquée comme payée');
            }

            // Transaction pour garantir la cohérence
            DB::transaction(function () use ($operation) {
                $operation->statut_courant = 'paid';
                $operation->date_paiement = now();
                $operation->save();

                Log::info('Opération marquée comme payée', [
                    'operation_id' => $operation->id,
                    'numero_operation' => $operation->numero_operation,
                    'montant' => $operation->montant,
                    'user_id' => Auth::id()
                ]);
            });

            return back()->with('success', 'Opération #' . $operation->numero_operation . ' marquée comme payée avec succès');

        } catch (\Exception $e) {
            Log::error('Erreur marquage paiement Compte Treso', [
                'error' => $e->getMessage(),
                'operation_id' => $id,
                'user_id' => Auth::id()
            ]);

            return back()->with('error', 'Erreur lors du marquage comme payé');
        }
    }

    /**
     * Voir les détails d'une opération
     */
    public function show($id)
    {
        $this->checkCompteTresoAccess();

        try {
            $operation = Operation::with(['service', 'typeOperation', 'user'])
                ->findOrFail($id);

            // Vérifier que l'opération est approuvée, à payer ou payée
            if (!in_array($operation->statut_courant, ['approved', 'a_payer', 'paid'])) {
                abort(403, 'Accès non autorisé à cette opération');
            }

            return view('comptetreso.show', compact('operation'));

        } catch (\Exception $e) {
            Log::error('Erreur affichage opération Compte Treso', [
                'error' => $e->getMessage(),
                'operation_id' => $id,
                'user_id' => Auth::id()
            ]);

            return back()->with('error', 'Erreur lors de l\'affichage de l\'opération');
        }
    }
}
