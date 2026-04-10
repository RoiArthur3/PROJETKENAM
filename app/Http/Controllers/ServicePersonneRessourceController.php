<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\User;
use App\Models\ServicePersonneRessource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServicePersonneRessourceController extends Controller
{
    /**
     * Afficher les personnes ressources d'un service
     */
    public function index(Service $service)
    {
        $personnesRessources = $service->personneRessources()
            ->with('user')
            ->get();

        $users = User::where('actif', true)
            ->orderBy('name')
            ->get();

        return view('services.personnes-ressources.index', compact('service', 'personnesRessources', 'users'));
    }

    /**
     * Ajouter une personne ressource à un service
     */
    public function store(Request $request, Service $service)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:chef_service,personne_ressource',
            'recevoir_emails' => 'boolean'
        ]);

        // Vérifier que l'utilisateur n'est pas déjà personne ressource pour ce service
        if (ServicePersonneRessource::where('service_id', $service->id)
            ->where('user_id', $request->user_id)
            ->exists()) {
            return back()->with('error', 'Cet utilisateur est déjà personne ressource pour ce service.');
        }

        // Si c'est un chef de service, vérifier qu'il n'y en a pas déjà un
        if ($request->role === 'chef_service') {
            $existingChef = ServicePersonneRessource::where('service_id', $service->id)
                ->where('role', 'chef_service')
                ->first();

            if ($existingChef) {
                return back()->with('error', 'Ce service a déjà un chef de service.');
            }
        }

        ServicePersonneRessource::create([
            'service_id' => $service->id,
            'user_id' => $request->user_id,
            'role' => $request->role,
            'recevoir_emails' => $request->recevoir_emails ?? true
        ]);

        return back()->with('success', 'Personne ressource ajoutée avec succès.');
    }

    /**
     * Mettre à jour une personne ressource
     */
    public function update(Request $request, Service $service, ServicePersonneRessource $personneRessource)
    {
        $request->validate([
            'role' => 'required|in:chef_service,personne_ressource',
            'recevoir_emails' => 'boolean'
        ]);

        // Si changement de rôle vers chef de service, vérifier qu'il n'y en a pas déjà un
        if ($request->role === 'chef_service' && $personneRessource->role !== 'chef_service') {
            $existingChef = ServicePersonneRessource::where('service_id', $service->id)
                ->where('role', 'chef_service')
                ->where('id', '!=', $personneRessource->id)
                ->first();

            if ($existingChef) {
                return back()->with('error', 'Ce service a déjà un chef de service.');
            }
        }

        $personneRessource->update([
            'role' => $request->role,
            'recevoir_emails' => $request->recevoir_emails ?? $personneRessource->recevoir_emails
        ]);

        return back()->with('success', 'Personne ressource mise à jour avec succès.');
    }

    /**
     * Supprimer une personne ressource
     */
    public function destroy(Service $service, ServicePersonneRessource $personneRessource)
    {
        $personneRessource->delete();
        return back()->with('success', 'Personne ressource supprimée avec succès.');
    }

    /**
     * Obtenir les emails des personnes ressources d'un service (API)
     */
    public function getEmails(Service $service)
    {
        $emails = $service->getEmailsPersonnesRessources();

        return response()->json([
            'service' => $service->nom,
            'emails' => $emails,
            'count' => count($emails)
        ]);
    }
}
