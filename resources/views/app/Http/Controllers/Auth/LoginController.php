<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
            'telephone' => 'required|string',
            'password' => 'required|string',
        ]);

        // Nettoyer le numéro de téléphone
        $phone = $this->cleanPhoneNumber($request->telephone);
        \Log::info('Tentative de connexion avec le numéro (nettoyé): ' . $phone);

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
        \Log::info('Variations du numéro testées: ' . json_encode($phoneVariations));

        // Rechercher l'utilisateur avec une seule requête optimisée
        $user = \App\Models\User::whereIn('telephone', $phoneVariations)->first();
        \Log::info('Utilisateur trouvé: ' . ($user ? 'Oui (ID: ' . $user->id . ')' : 'Non'));

        // Vérifier si l'utilisateur existe et si le mot de passe est correct
        if ($user) {
            \Log::info('Vérification du mot de passe pour l\'utilisateur ID: ' . $user->id);
            $passwordMatch = \Illuminate\Support\Facades\Hash::check($request->password, $user->password);
            \Log::info('Mot de passe ' . ($passwordMatch ? 'valide' : 'incorrect'));

            if ($passwordMatch) {
                if ($user->is_active) {
                    auth()->login($user, $request->filled('remember'));
                    $request->session()->regenerate();

                    $user = auth()->user();

                    if ($user->role === 'superadmin') {
                        return redirect()->intended('/dashboard');
                    }

                    if ($user->role === 'admin') {
                        return redirect()->intended('/operations/dashboard');
                    }

                    // autres rôles
                    return redirect()->intended('/operations');
                }

                return back()->withErrors([
                    'telephone' => 'Votre compte est désactivé. Veuillez contacter l\'administrateur.',
                ]);
            }

            // Log des erreurs
            \Log::warning('Mot de passe incorrect pour l\'utilisateur ID: ' . $user->id);

            return back()->withErrors([
                'telephone' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
            ]);
        }

        // Log des erreurs
        \Log::warning('Aucun utilisateur trouvé avec les variations de numéro: ' . json_encode($phoneVariations));

        return back()->withErrors([
            'telephone' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ]);
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
