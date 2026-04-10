<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // Charger les services actifs
        $services = \App\Models\Service::actif()->orderBy('nom')->get();
        
        // Charger tous les modules disponibles
        $allModules = \App\Services\UserPermissionService::getAllModules();
        
        return view('auth.register', compact('services', 'allModules'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required', 'string', 'max:30', 'regex:/^(\+225\s?)?[0-9\s]{10,15}$/'],
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'role' => ['required', 'string', 'in:agent,moderator'],
            'modules' => ['nullable', 'array'],
            'modules.*' => ['string'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'service_id' => $request->service_id,
            'role' => $request->role,
            'is_active' => true,
        ]);

        event(new Registered($user));

        // Définir les permissions selon le rôle
        if ($request->role === 'agent') {
            // Pour les agents : opérations et requêtes auto-sélectionnés
            $selectedModules = ['operations', 'requetes'];
        } else {
            // Pour les modérateurs : modules sélectionnés par l'utilisateur
            $selectedModules = $request->modules ?? [];
        }

        // Mettre à jour les permissions
        \App\Services\UserPermissionService::updateUserPermissions($user, $selectedModules);

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
