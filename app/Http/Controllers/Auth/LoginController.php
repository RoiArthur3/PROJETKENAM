<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Nettoie le numéro de téléphone
     *
     * @param string $phone
     * @return string
     */
    protected function cleanPhoneNumber($phone)
    {
        // Supprimer tous les caractères non numériques
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Supprimer le préfixe 225 s'il existe
        if (strpos($phone, '225') === 0) {
            $phone = substr($phone, 3);
        }

        // S'assurer que le numéro commence par 0
        if (strpos($phone, '0') !== 0) {
            $phone = '0' . $phone;
        }

        return $phone;
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        // Nettoyer le numéro de téléphone
        $phone = $this->cleanPhoneNumber($request->phone);
        Log::info('Tentative de connexion avec le numéro (nettoyé): ' . $phone);

        $phoneColumn = $this->resolvePhoneColumn();
        if ($phoneColumn === null) {
            return back()->withErrors([
                'phone' => 'Configuration incomplète: la colonne telephone/phone est absente de la table users.',
            ]);
        }

        // Créer les variations possibles du numéro
        $phoneVariations = [
            $phone,
            '0' . ltrim($phone, '0'), // Format avec 0 initial
            '225' . ltrim($phone, '0'), // Format avec 225
            '+225' . ltrim($phone, '0'), // Format avec +225
            '00225' . ltrim($phone, '0') // Format avec 00225
        ];

        // Supprimer les doublons
        $phoneVariations = array_unique($phoneVariations);
        Log::info('Variations du numéro testées: ' . json_encode($phoneVariations));

        // Rechercher l'utilisateur avec une seule requête optimisée
        $user = \App\Models\User::whereIn($phoneColumn, $phoneVariations)->first();
        Log::info('Utilisateur trouvé: ' . ($user ? 'Oui (ID: ' . $user->id . ')' : 'Non'));

        // Vérifier si l'utilisateur existe et si le mot de passe est correct
        if ($user) {
            Log::info('Vérification du mot de passe pour l\'utilisateur ID: ' . $user->id);
            $passwordMatch = \Illuminate\Support\Facades\Hash::check($request->password, $user->password);
            Log::info('Mot de passe ' . ($passwordMatch ? 'valide' : 'incorrect'));

            if ($passwordMatch) {
                $isActive = !Schema::hasColumn('users', 'is_active') || (bool) $user->is_active;
                if ($isActive) {
                    // Forcer "Remember Me" à true pour que la session n'expire jamais
                    Auth::login($user, true);

                    // Rechargement automatique des permissions à chaque connexion (Persistance)
                    \App\Services\UserPermissionService::reloadUserPermissionsFromDatabase($user);

                    $request->session()->regenerate();

                    $user = Auth::user();

                    // Redirection simple vers le dashboard pour le superadmin
                    if ($user->role === 'superadmin' || $user->role === 'admin') {
                        return redirect()->intended('/dashboard');
                    }

                    // Redirection par défaut pour les autres rôles
                    return redirect()->intended('/dashboard');
                }

                return back()->withErrors([
                    'phone' => 'Votre compte est désactivé. Veuillez contacter l\'administrateur.',
                ]);
            }

            // Log des erreurs
            Log::warning('Mot de passe incorrect pour l\'utilisateur ID: ' . $user->id);

            return back()->withErrors([
                'phone' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
            ]);
        }

        // Log des erreurs
        Log::warning('Aucun utilisateur trouvé avec les variations de numéro: ' . json_encode($phoneVariations));

        return back()->withErrors([
            'phone' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    protected function resolvePhoneColumn(): ?string
    {
        if (Schema::hasColumn('users', 'telephone')) {
            return 'telephone';
        }

        if (Schema::hasColumn('users', 'phone')) {
            return 'phone';
        }

        return null;
    }
}
