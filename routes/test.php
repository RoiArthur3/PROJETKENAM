<?php

use Illuminate\Support\Facades\Route;

// Route de test pour la connexion
Route::get('/test-login', function () {
    return view('test-login');
});

Route::post('/test-login-submit', function () {
    $credentials = request()->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (auth()->attempt($credentials)) {
        request()->session()->regenerate();

        $user = auth()->user();
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ],
            'redirect' => $user->hasRole('agent') ? '/agent/requetes/dashboard' : '/dashboard'
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Identifiants incorrects'
    ]);
});
