<?php

namespace App\Http\Controllers;

use App\Models\FacialDevice;
use App\Models\FacialEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FacialDeviceController extends Controller
{
    public function index()
    {
        $devices = FacialDevice::orderBy('created_at', 'desc')->get();
        return view('rh.facial-devices.index', compact('devices'));
    }

    public function create()
    {
        return view('rh.facial-devices.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'serial_number' => 'required|string|max:100|unique:facial_devices,serial_number',
            'ip_address' => 'required|ip',
            'port' => 'nullable|integer|min:1|max:65535',
            'protocol' => 'required|in:http,https',
            'username' => 'nullable|string|max:100',
            'password' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['api_token'] = Str::random(80);
        $validated['port'] = $validated['port'] ?? 80;
        $validated['is_active'] = $validated['is_active'] ?? true;

        FacialDevice::create($validated);

        return redirect()->route('rh.facial-devices.index')
            ->with('success', 'Terminal ajouté avec succès.');
    }

    public function show(FacialDevice $device)
    {
        $recentEvents = $device->events()
            ->orderByDesc('event_time')
            ->limit(20)
            ->get();

        return view('rh.facial-devices.show', compact('device', 'recentEvents'));
    }

    public function edit(FacialDevice $device)
    {
        return view('rh.facial-devices.edit', compact('device'));
    }

    public function update(Request $request, FacialDevice $device)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'serial_number' => 'required|string|max:100|unique:facial_devices,serial_number,' . $device->id,
            'ip_address' => 'required|ip',
            'port' => 'nullable|integer|min:1|max:65535',
            'protocol' => 'required|in:http,https',
            'username' => 'nullable|string|max:100',
            'password' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['port'] = $validated['port'] ?? 80;
        $validated['is_active'] = $validated['is_active'] ?? true;

        $device->update($validated);

        return redirect()->route('rh.facial-devices.index')
            ->with('success', 'Terminal mis à jour avec succès.');
    }

    public function destroy(FacialDevice $device)
    {
        $device->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Terminal supprimé avec succès.',
            ]);
        }

        return redirect()->route('rh.facial-devices.index')
            ->with('success', 'Terminal supprimé avec succès.');
    }

    public function regenerateToken(FacialDevice $device)
    {
        $device->update(['api_token' => Str::random(80)]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Token API régénéré.',
                'token' => $device->api_token,
            ]);
        }

        return back()->with('success', 'Token API régénéré.');
    }

    public function toggleStatus(FacialDevice $device)
    {
        $device->update(['is_active' => !$device->is_active]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Statut du terminal mis à jour.',
                'is_active' => $device->is_active,
            ]);
        }

        return back()->with('success', 'Statut du terminal mis à jour.');
    }
}
