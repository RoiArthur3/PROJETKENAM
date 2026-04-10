<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Afficher le formulaire de connexion
     */
    public function showLoginForm()
    {
        return view('auth.phone-login-new');
    }

    /**
     * Traiter la tentative de connexion
     */
    public function login(Request $request)
    {
        // Validation des entrées
        $request->validate([
            'telephone' => 'required|string|min:9|max:10', // Téléphone uniquement
            'password' => 'required|string|min:6',
        ], [
            'telephone.required' => 'Le numéro de téléphone est obligatoire',
            'password.required' => 'Le mot de passe est obligatoire',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères',
        ]);

        $telephone = $request->telephone;

        // Nettoyer le numéro de téléphone
        $phone = preg_replace('/[^0-9]/', '', $telephone);

        // Créer les variations possibles du numéro
        $phoneVariations = [
            $phone,
            '0' . ltrim($phone, '0'), // Format avec 0 initial
            '225' . ltrim($phone, '0'), // Format avec 225
            '+225' . ltrim($phone, '0'), // Format avec +225
            '00225' . ltrim($phone, '0') // Format avec 00225
        ];

        // Rechercher l'utilisateur avec une seule requête optimisée
        $user = \App\Models\User::whereIn('telephone', $phoneVariations)->first();
        \Log::info('Utilisateur trouvé: ' . ($user ? 'Oui (ID: ' . $user->id . ')' : 'Non'));

        if (!$user) {
            return back()->withErrors([
                'telephone' => 'Aucun compte trouvé avec ce numéro de téléphone.',
            ])->withInput($request->only('telephone'));
        }

        // Vérifier si le compte est actif
        if (!$user->is_active) {
            return back()->withErrors([
                'telephone' => 'Ce compte est désactivé. Veuillez contacter l\'administrateur.',
            ])->withInput($request->only('telephone'));
        }

        // Vérifier le mot de passe
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'telephone' => 'Le mot de passe est incorrect.',
            ])->withInput($request->only('telephone'));
        }

        // Connexion de l'utilisateur
        Auth::login($user, $request->filled('remember'));
        $request->session()->regenerate();

        // Mettre à jour la date de dernière connexion
        $user->update(['last_login_at' => now()]);

        // Vérifier s'il y a une redirection spécifique
        if ($request->has('redirect') && !empty($request->redirect)) {
            return redirect($request->redirect);
        }

        // Redirection selon le rôle
        return $this->redirectUser($user);
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Vous avez été déconnecté avec succès.');
    }

    /**
     * Formater le numéro de téléphone
     */
    private function formatPhoneNumber($phone)
    {
        // Supprimer tous les caractères non numériques
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // S'assurer que le numéro commence par 0
        if (strlen($phone) === 10 && !str_starts_with($phone, '0')) {
            $phone = '0' . $phone;
        }

        return $phone;
    }

    /**
     * Rediriger l'utilisateur selon son rôle
     */
    private function redirectUser($user)
    {
        switch ($user->role) {
            case 'superadmin':
                return redirect()->intended(route('dashboard'));
            case 'admin':
                return redirect()->intended(route('dashboard'));
            case 'moderator':
            case 'moderateur':
                // Rediriger vers le dashboard principal (avec design d'origine)
                return redirect()->intended(route('dashboard'));
            case 'agent':
                return redirect()->intended(route('operations.index'));
            default:
                return redirect()->intended(route('operations.index'));
        }
    }
}
