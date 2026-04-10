<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminAgentController extends Controller
{
    /**
     * Display a listing of agents.
     */
    public function index()
    {
        $agents = Agent::with('service')->orderBy('nom')->paginate(15);
        $services = Service::orderBy('ordre')->get();

        return view('admin.agents.index', compact('agents', 'services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $services = Service::orderBy('ordre')->get();
        $users = User::whereHasRole(['admin', 'superadmin'])->orderBy('nom')->get();

        return view('admin.agents.create', compact('services', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string',
            'poste' => 'nullable|string|max:20',
            'salaire' => 'nullable|numeric|min:0',
            'service_id' => 'required|exists:services,id',
            'statut' => 'required|in:actif,inactif',
            'photo' => 'nullable|image|mimes:2048',
            'notes' => 'nullable|string'
        ]);

        $validated['password'] = Hash::make('password');

        $agent = Agent::create($validated);

        return redirect()->route('admin.agents.index')->with('success', 'Agent créé avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $agent = Agent::with(['service', 'user'])->findOrFail($id);

        return view('admin.agents.show', compact('agent'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $agent = Agent::findOrFail($id);
        $services = Service::orderBy('ordre')->get();
        $users = User::whereHasRole(['admin', 'superadmin'])->orderBy('nom')->get();

        return view('admin.agents.edit', compact('agent', 'services', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$id,
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string',
            'poste' => 'nullable|string|max:20',
            'salaire' => 'nullable|numeric|min:0',
            'service_id' => 'required|exists:services,id',
            'statut' => 'required|in:actif,inactif',
            'photo' => 'nullable|image|mimes:2048',
            'notes' => 'nullable|string'
        ]);

        $agent = Agent::findOrFail($id);
        $agent->update($validated);

        return redirect()->route('admin.agents.index')->with('success', 'Agent mis à jour avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $agent = Agent::findOrFail($id);
        $agent->delete();

        return redirect()->route('admin.agents.index')->with('success', 'Agent supprimé avec succès');
    }

    /**
     * Toggle agent status.
     */
    public function toggleStatus($id)
    {
        $agent = Agent::findOrFail($id);
        $agent->statut = $agent->statut === 'actif' ? 'inactif' : 'actif';
        $agent->save();

        return redirect()->route('admin.agents.index')->with('success', 'Statut de l\'agent mis à jour');
    }

    /**
     * Reset agent password.
     */
    public function resetPassword($id)
    {
        $newPassword = 'Agent' . rand(1000, 9999);
        $agent->update(['password' => Hash::make($newPassword)]);

        return redirect()->back()
            ->with('success', "Mot de passe réinitialisé pour {$agent->nom_complet}: {$newPassword}");
    }
}
