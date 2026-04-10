<?php

namespace App\Http\Controllers;

use App\Models\OperationalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OperationalServiceController extends Controller
{
    public function index()
    {
        $services = OperationalService::orderBy('ordre')->orderBy('nom')->get();
        return view('admin.operational-services', compact('services'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:30|unique:operational_services,code',
            'nom' => 'required|string|max:150',
            'description' => 'nullable|string|max:500',
            'ordre' => 'nullable|integer|min:1',
            'actif' => 'nullable|boolean',
        ]);

        $data['code'] = strtoupper($data['code']);
        $data['actif'] = $request->boolean('actif', true);
        $data['ordre'] = $data['ordre'] ?? 1;

        OperationalService::create($data);
        Cache::forget('operational_services.active');

        return back()->with('status', 'Service opérationnel créé');
    }

    public function toggle(OperationalService $service)
    {
        $service->update(['actif' => !$service->actif]);
        Cache::forget('operational_services.active');
        return back()->with('status', 'Service mis à jour');
    }

    public function destroy(OperationalService $service)
    {
        // Sécurité minimale: empêcher la suppression si lié à des opérations
        if (method_exists($service, 'operations') && $service->operations()->exists()) {
            return back()->with('error', 'Impossible de supprimer: des opérations y sont liées');
        }
        $service->delete();
        Cache::forget('operational_services.active');
        return back()->with('status', 'Service supprimé');
    }
}
