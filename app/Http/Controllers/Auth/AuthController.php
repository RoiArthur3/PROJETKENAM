<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\AuthService;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Modules testés pour déterminer une redirection post-login sûre.
     */
    private const REDIRECT_MODULES = [
        'operations',
        'validations',
        'tresorerie',
        'accounting',
        'comptabilite',
        'rh',
        'materiel',
        'warehouse',
        'commercial',
        'fournisseurs',
        'projects',
    ];

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
            'telephone' => 'required|string|regex:/^0[0-9]{9}$/', // exactement 10 chiffres, commence par 0
            'password' => 'required|string|min:6',
        ], [
            'telephone.required' => 'Le numéro de téléphone est obligatoire',
            'telephone.regex' => 'Le numéro de téléphone doit contenir exactement 10 chiffres et commencer par 0',
            'password.required' => 'Le mot de passe est obligatoire',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères',
        ]);

        // Recherche de l'utilisateur par téléphone
        $user = AuthService::findUserByPhone($request->telephone);

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

        // Journaliser la connexion
        AuthService::logLogin($user);

        // Vérifier s'il y a une redirection spécifique
        if ($request->has('redirect') && !empty($request->redirect)) {
            return redirect($request->redirect);
        }

        $redirectUrl = $this->resolveRedirectUrl($user);

        if ($redirectUrl === null) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'telephone' => 'Ce compte ne dispose d\'aucun module autorisé. Veuillez contacter l\'administrateur.',
            ])->withInput($request->only('telephone'));
        }

        return redirect()->intended($redirectUrl);
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

    private function resolveRedirectUrl(User $user): ?string
    {
        if ($user->role === 'superadmin') {
            return '/dashboard';
        }

        return '/mon-profil';
    }
}
