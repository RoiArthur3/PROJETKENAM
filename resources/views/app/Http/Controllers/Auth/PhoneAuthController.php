<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PhoneAuthController extends Controller
{
    /**
     * Affiche le formulaire de connexion par téléphone
     */
    public function showLoginForm()
    {
        // Si l'utilisateur est déjà connecté, le rediriger en fonction de son rôle
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }

        return view('auth.phone-login');
    }

    /**
     * Traite la tentative de connexion
     */
    public function login(Request $request)
    {
        Log::info('Tentative de connexion', ['phone' => $request->phone]);

        $request->validate([
            'phone' => 'required|string|min:10|max:13',
            'password' => 'required|string',
        ]);

        // Nettoyer le numéro de téléphone
        $phone = preg_replace('/[^0-9]/', '', $request->phone);
        Log::info('Numéro nettoyé', ['phone' => $phone]);

        // Créer les variations possibles du numéro
        $phoneVariations = [
            $phone,
            '0' . $phone,
            '225' . $phone,
            '+225' . $phone
        ];
        Log::info('Variations du numéro', $phoneVariations);

        // Rechercher l'utilisateur (uniquement dans telephone maintenant)
        $user = User::whereIn('telephone', $phoneVariations)->first();
        Log::info('Utilisateur trouvé', ['user' => $user ? $user->id : null]);

        // Vérifier si l'utilisateur existe et si le mot de passe est correct
        if ($user) {
            Log::info('Vérification du mot de passe', ['match' => Hash::check($request->password, $user->password)]);

            if (Hash::check($request->password, $user->password)) {
                // Vérifier si le compte est actif
                if (isset($user->is_active) && !$user->is_active) {
                    throw ValidationException::withMessages([
                        'phone' => 'Ce compte est désactivé. Veuillez contacter l\'administrateur.',
                    ]);
                }

                // Vérifier si l'utilisateur a un rôle valide
                if (!isset($user->role) || empty($user->role)) {
                    throw ValidationException::withMessages([
                        'phone' => 'Votre compte n\'a pas de rôle défini. Veuillez contacter l\'administrateur.',
                    ]);
                }

                // Connecter l'utilisateur
                Auth::login($user, $request->filled('remember'));
                $request->session()->regenerate();

                // Journaliser la connexion réussie
                Log::info('Connexion réussie', [
                    'user_id' => $user->id,
                    'role' => $user->role,
                    'ip' => $request->ip()
                ]);

                // Rediriger en fonction du rôle
                return $this->redirectToDashboard();
            }
        }

        // En cas d'échec de l'authentification
        Log::warning('Échec de l\'authentification', [
            'phone' => $phone,
            'ip' => $request->ip()
        ]);

        throw ValidationException::withMessages([
            'phone' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ]);
    }

    /**
     * Redirige l'utilisateur vers le tableau de bord approprié en fonction de son rôle
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectToDashboard()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('/login');
        }

        // Définir les redirections par défaut pour chaque rôle
        $redirects = [
            'superadmin' => '/dashboard',
            'admin' => '/dashboard',
            'agent' => '/operations/dashboard',
            'user' => '/operations/dashboard',
        ];

        // Obtenir l'URL de redirection en fonction du rôle, ou la page d'accueil par défaut
        $redirectTo = $redirects[$user->role] ?? '/';

        return redirect()->intended($redirectTo);
    }
}
