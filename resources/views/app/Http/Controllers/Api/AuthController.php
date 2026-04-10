<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Authentification pour l'application mobile
     */
    public function login(Request $request)
    {
        $request->validate([
            'telephone' => 'required',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = User::where('telephone', $request->telephone)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'telephone' => ['Les identifiants sont incorrects.'],
            ]);
        }

        if (!$user->is_active) {
            return response()->json(['message' => 'Votre compte est désactivé.'], 403);
        }

        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'service_id' => $user->service_id,
            ]
        ]);
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Déconnecté avec succès.']);
    }

    /**
     * Profil de l'utilisateur connecté
     */
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }
}
