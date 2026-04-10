<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AuthService
{
    /**
     * Validation du numéro de téléphone
     */
    public static function validatePhoneNumber($phone)
    {
        // Exactement 10 chiffres commençant par 0
        return preg_match('/^0[0-9]{9}$/', $phone);
    }

    /**
     * Formatage du numéro de téléphone
     */
    public static function formatPhoneNumber($phone)
    {
        // Supprimer tous les caractères non numériques
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Si le numéro commence par 225 (indicatif CI), le remplacer par 0
        if (strlen($phone) === 12 && str_starts_with($phone, '225')) {
            $phone = '0' . substr($phone, 3);
        }

        // Si le numéro commence par +225, le remplacer par 0
        if (str_starts_with($phone, '+225')) {
            $phone = '0' . substr($phone, 4);
        }

        // Si le numéro a 9 chiffres (sans le 0 initial), ajouter le 0
        if (strlen($phone) === 9) {
            $phone = '0' . $phone;
        }

        // Conserver uniquement le format local strict attendu (10 chiffres commençant par 0)
        if (!preg_match('/^0[0-9]{9}$/', $phone)) {
            return $phone;
        }

        return $phone;
    }

    /**
     * Recherche d'utilisateur par téléphone
     */
    public static function findUserByPhone($phone)
    {
        $phoneColumn = self::resolvePhoneColumn();
        if ($phoneColumn === null) {
            return null;
        }

        $phone = self::formatPhoneNumber($phone);

        // Créer les variations possibles du numéro
        $phoneVariations = [
            $phone,
            '0' . ltrim($phone, '0'), // Format avec 0 initial
            '225' . ltrim($phone, '0'), // Format avec 225
            '+225' . ltrim($phone, '0'), // Format avec +225
            '00225' . ltrim($phone, '0') // Format avec 00225
        ];

        return User::whereIn($phoneColumn, $phoneVariations)->first();
    }

    /**
     * Vérification des permissions de l'utilisateur
     */
    public static function checkUserPermissions($user, $module)
    {
        // 1. Superadmin : Accès total
        if ($user->role === 'superadmin') {
            return true;
        }

        // 2. Admin/Modérateur : vérifier via le modèle (JSON + Colonnes)
        return $user->canAccessModule($module);
    }

    /**
     * Journalisation de la connexion
     */
    public static function logLogin($user)
    {
        $phoneValue = $user->telephone ?? $user->phone ?? null;

        Log::info('Connexion réussie', [
            'user_id' => $user->id,
            'name' => $user->name,
            'role' => $user->role,
            'telephone' => $phoneValue,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    private static function resolvePhoneColumn(): ?string
    {
        if (Schema::hasColumn('users', 'telephone')) {
            return 'telephone';
        }

        if (Schema::hasColumn('users', 'phone')) {
            return 'phone';
        }

        return null;
    }

    /**
     * Détermination de la route de redirection
     */
    public static function getRedirectRoute($user)
    {
        switch ($user->role) {
            case 'superadmin':
                // Les superadmins vont au tableau de bord principal
                return 'dashboard';

            case 'admin':
                // L'admin peut voir le dashboard général.
                return 'dashboard';

            case 'moderator':
            case 'moderateur':
                // Les modérateurs vont au dashboard des opérations
                return 'operations.dashboard';

            case 'agent':
                // Les agents vont à la liste de leurs opérations
                return 'operations.index';

            default:
                Log::warning('Rôle non reconnu', ['user_id' => $user->id, 'role' => $user->role]);
                return 'operations.index';
        }
    }

}
