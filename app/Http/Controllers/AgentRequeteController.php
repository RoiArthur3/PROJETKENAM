<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Operation;

class AgentRequeteController extends Controller
{
    /**
     * Afficher les requêtes de l'agent connecté
     */
    public function index()
    {
        $user = Auth::user();

        // Récupérer uniquement les requêtes créées par l'agent connecté
        $requetes = Operation::where('created_by', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('requetes.suivi-validation', compact('requetes'));
    }
}
