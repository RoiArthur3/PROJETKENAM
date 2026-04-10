<?php

namespace App\Http\Controllers;

use App\Models\ServiceOperationnel;
use Illuminate\Http\Request;

class ServiceOperationnelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $services = ServiceOperationnel::orderBy('nom')->get();
        } catch (\Exception $e) {
            $services = collect([]); // Collection vide si la table n'existe pas
        }
        return view('admin.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.services.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:services_operationnels,email',
            'telephone' => 'nullable|string|max:20',
            'responsable' => 'nullable|string|max:100',
            'couleur' => 'nullable|string|max:7',
            'icone' => 'nullable|string|max:50',
            'actif' => 'boolean',
        ]);

        // Générer un mot de passe aléatoire sécurisé
        $generatedPassword = $this->generateRandomPassword();
        $validated['password'] = bcrypt($generatedPassword);

        // Générer un code unique
        $validated['code'] = 'SRV-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

        ServiceOperationnel::create($validated);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service créé avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(ServiceOperationnel $serviceOperationnel)
    {
        return view('admin.services.show', compact('serviceOperationnel'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceOperationnel $serviceOperationnel)
    {
        return view('admin.services.edit', ['service' => $serviceOperationnel]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceOperationnel $serviceOperationnel)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:services_operationnels,email,'.$serviceOperationnel->id,
            'telephone' => 'nullable|string|max:20',
            'responsable' => 'nullable|string|max:100',
            'couleur' => 'nullable|string|max:7',
            'icone' => 'nullable|string|max:50',
            'actif' => 'boolean',
        ]);

        // Ajouter le champ actif manuellement
        $validated['actif'] = $request->has('actif');

        $serviceOperationnel->update($validated);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service mis à jour avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceOperationnel $serviceOperationnel)
    {
        try {
            $serviceOperationnel->delete();
            return redirect()->route('admin.services.index')
                ->with('success', 'Service supprimé avec succès');
        } catch (\Exception $e) {
            return redirect()->route('admin.services.index')
                ->with('error', 'Impossible de supprimer ce service. Il est peut-être utilisé dans des opérations.');
        }
    }

    /**
     * Générer un mot de passe aléatoire sécurisé
     */
    private function generateRandomPassword($length = 12)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*';
        $charactersLength = strlen($characters);
        $randomPassword = '';
        for ($i = 0; $i < $length; $i++) {
            $randomPassword .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomPassword;
    }
}
