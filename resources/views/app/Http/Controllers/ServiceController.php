<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceOperationnel;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = ServiceOperationnel::orderBy('nom')->get();
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
            'nom' => 'required|string|max:255|unique:services_operationnels,nom',
            'description' => 'nullable|string',
            'email' => 'nullable|email|unique:services_operationnels,email',
            'telephone' => 'nullable|string|max:20',
            'responsable' => 'nullable|string|max:255',
            'couleur' => 'nullable|string|max:7',
            'icone' => 'nullable|string|max:50',
            'actif' => 'boolean',
        ]);

        // Gérer le checkbox actif
        $validated['actif'] = $request->has('actif') ? 1 : 0;

        // Générer un code unique si non fourni
        if (!isset($validated['code']) || empty($validated['code'])) {
            $validated['code'] = strtoupper(substr(str_replace(' ', '', $validated['nom']), 0, 3)) . rand(100, 999);
        }

        try {
            $service = ServiceOperationnel::create($validated);

            return redirect('/admin/services')
                ->with('success', 'Service créé avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création du service: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ServiceOperationnel $service)
    {
        return view('admin.services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceOperationnel $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceOperationnel $service)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:services_operationnels,nom,' . $service->id,
            'description' => 'nullable|string',
            'email' => 'nullable|email|unique:services_operationnels,email,' . $service->id,
            'telephone' => 'nullable|string|max:20',
            'responsable' => 'nullable|string|max:255',
            'actif' => 'required|boolean',
        ]);

        try {
            $service->update($validated);

            return redirect('/admin/services')
                ->with('success', 'Service mis à jour avec succès.');

        } catch (\Exception $e) {
            return redirect("/admin/services/{$service->id}/edit")
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour du service: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceOperationnel $service)
    {
        try {
            $service->delete();

            return redirect()->route('admin.services.index')
                ->with('success', 'Service supprimé avec succès.');

        } catch (\Exception $e) {
            return redirect()->route('admin.services.index')
                ->with('error', 'Une erreur est survenue lors de la suppression du service: ' . $e->getMessage());
        }
    }
}
