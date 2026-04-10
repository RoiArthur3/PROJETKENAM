<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class ServiceAuthController extends Controller
{
    /**
     * Afficher le formulaire de connexion pour les services
     */
    public function showLoginForm()
    {
        return view('services.auth.login');
    }

    /**
     * Traiter la tentative de connexion du service
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);

        $service = Service::where('email', $request->email)
                         ->where('actif', true)
                         ->first();

        if (!$service || !$service->password) {
            return back()->withErrors([
                'email' => 'Aucun service trouvé avec cet email ou le service n\'est pas actif.'
            ])->withInput($request->except('password'));
        }

        if (!Hash::check($request->password, $service->password)) {
            return back()->withErrors([
                'password' => 'Le mot de passe est incorrect.'
            ])->withInput($request->except('password'));
        }

        // Stocker le service connecté en session
        Session::put('service_authenticated', true);
        Session::put('authenticated_service', $service);
        Session::put('service_id', $service->id);

        return redirect()->route('services.dashboard');
    }

    /**
     * Afficher le tableau de bord du service
     */
    public function dashboard()
    {
        if (!Session::get('service_authenticated')) {
            return redirect()->route('services.login')->with('error', 'Veuillez vous connecter d\'abord.');
        }

        $service = Session::get('authenticated_service');

        return view('services.dashboard', compact('service'));
    }

    /**
     * Déconnexion du service
     */
    public function logout()
    {
        Session::forget('service_authenticated');
        Session::forget('authenticated_service');
        Session::forget('service_id');

        return redirect()->route('services.login')->with('success', 'Vous avez été déconnecté avec succès.');
    }

    /**
     * Afficher le profil du service
     */
    public function profile()
    {
        if (!Session::get('service_authenticated')) {
            return redirect()->route('services.login')->with('error', 'Veuillez vous connecter d\'abord.');
        }

        $service = Session::get('authenticated_service');

        return view('services.profile', compact('service'));
    }

    /**
     * Mettre à jour le profil du service
     */
    public function updateProfile(Request $request)
    {
        if (!Session::get('service_authenticated')) {
            return redirect()->route('services.login')->with('error', 'Veuillez vous connecter d\'abord.');
        }

        $service = Session::get('authenticated_service');

        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:services,email,' . $service->id,
            'phone' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'current_password' => 'nullable|required_with:new_password|string',
            'new_password' => 'nullable|string|min:6|confirmed'
        ]);

        // Mettre à jour les informations de base
        $service->nom = $request->nom;
        $service->email = $request->email;
        $service->phone = $request->phone;
        $service->description = $request->description;

        // Mettre à jour le mot de passe si fourni
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $service->password)) {
                return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
            }
            $service->password = Hash::make($request->new_password);
        }

        $service->save();

        // Mettre à jour la session
        Session::put('authenticated_service', $service->fresh());

        return back()->with('success', 'Votre profil a été mis à jour avec succès.');
    }
}
