<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Route de test d'authentification
Route::get('/test-auth', function () {
    // Chercher l'utilisateur
    $user = User::whereIn('telephone', ['0554419341', '554419341', '2250554419341', '+2250554419341'])->first();
    
    if (!$user) {
        return response()->json([
            'status' => 'ERROR',
            'message' => 'Utilisateur NOT FOUND',
            'searched_phones' => ['0554419341', '554419341', '2250554419341', '+2250554419341'],
            'total_users' => User::count(),
            'sample_users' => User::limit(5)->get(['id', 'name', 'telephone', 'email']),
        ]);
    }

    // Vérifier le mot de passe
    $password = 'admin123';
    $passwordMatch = Hash::check($password, $user->password);

    return response()->json([
        'status' => 'FOUND',
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'telephone' => $user->telephone,
            'email' => $user->email,
            'role' => $user->role,
            'is_active' => $user->is_active ?? true,
        ],
        'password_test' => [
            'password_match' => $passwordMatch,
            'hashed_password_stored' => substr($user->password, 0, 20) . '...',
        ],
        'hint' => $passwordMatch ? 'Password MATCHES! Login should work.' : 'Password DOES NOT match. Try resetting it.'
    ]);
});
