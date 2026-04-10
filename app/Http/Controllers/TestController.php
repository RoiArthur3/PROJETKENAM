<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Operation;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
    public function pending()
    {
        $operations = Operation::with(['initiateur', 'services'])
            ->where(function($query) {
                $query->where('statut_courant', 'en_attente')
                      ->orWhere('statut_courant', 'pending_validation');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Debug
        dd([
            'user_role' => Auth::user() ? Auth::user()->role : 'not logged',
            'operations_count' => $operations->count(),
            'operations_total' => $operations->total(),
            'first_operation' => $operations->first() ? [
                'id' => $operations->first()->id,
                'titre' => $operations->first()->titre,
                'statut_courant' => $operations->first()->statut_courant
            ] : null
        ]);

        return view('validations.pending', compact('operations'));
    }
}
