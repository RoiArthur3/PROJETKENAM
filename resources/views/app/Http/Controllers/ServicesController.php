<?php

namespace App\Http\Controllers;

use App\Models\ServiceOperationnel;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    public function index()
    {
        $services = ServiceOperationnel::orderBy('nom')->get();
        return view('services.index', compact('services'));
    }

    /**
     * API pour lister les services (pour le select dynamique)
     */
    public function apiList()
    {
        $services = ServiceOperationnel::where('actif', true)
            ->select('id', 'nom', 'email')
            ->orderBy('nom')
            ->get();

        return response()->json([
            'services' => $services
        ]);
    }

    public function create()
    {
        return view('services.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:services_operationnels,email',
            'telephone' => 'nullable|string|max:20',
            'responsable' => 'nullable|string|max:100',
            'couleur' => 'nullable|string|max:7',
            'icone' => 'nullable|string|max:50',
        ]);

        // Ajouter le champ actif manuellement
        $validated['actif'] = $request->has('actif');

        // Générer un mot de passe aléatoire sécurisé
        $generatedPassword = $this->generateRandomPassword();
        $validated['password'] = bcrypt($generatedPassword);

        // Générer un code unique
        $validated['code'] = 'SRV-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

        ServiceOperationnel::create($validated);

        return redirect()->route('services.index')
            ->with('success', 'Service créé avec succès');
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

    public function edit(ServiceOperationnel $service)
    {
        return view('services.edit', compact('service'));
    }

    public function update(Request $request, ServiceOperationnel $service)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:services_operationnels,email,'.$service->id,
            'telephone' => 'nullable|string|max:20',
            'responsable' => 'nullable|string|max:100',
            'couleur' => 'nullable|string|max:7',
            'icone' => 'nullable|string|max:50',
        ]);

        // Ajouter le champ actif manuellement
        $validated['actif'] = $request->has('actif');

        $service->update($validated);

        return redirect()->route('services.index')
            ->with('success', 'Service mis à jour avec succès');
    }

    public function destroy(ServiceOperationnel $service)
    {
        try {
            if (!$service) {
                $message = 'Service non trouvé';
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json(['error' => $message], 404);
                }
                return redirect()->route('services.index')
                    ->with('error', $message);
            }

            $serviceName = $service->nom;
            $service->delete();

            $message = "Service '{$serviceName}' supprimé avec succès";

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => $message]);
            }

            return redirect()->route('services.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            $message = 'Erreur lors de la suppression: ' . $e->getMessage();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['error' => $message], 500);
            }

            return redirect()->route('services.index')
                ->with('error', $message);
        }
    }
}
