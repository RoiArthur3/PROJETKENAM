<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicule;
use App\Models\Client;

class ControleController extends Controller
{
    /**
     * Afficher le formulaire de création d'un contrôle
     */
    public function create()
    {
        $vehicules = Vehicule::orderBy('immatriculation')->get();
        $clients = Client::orderBy('nom')->get();
        
        return view('controles.create', compact('vehicules', 'clients'));
    }
}
