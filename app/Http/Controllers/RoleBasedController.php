<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RoleBasedController extends Controller
{
    /**
     * Afficher le tableau de bord selon le rôle de l'utilisateur
     */
    public function dashboard()
    {
        $user = auth()->user();

        // Rediriger selon le rôle
        switch ($user->role) {
            case 'superadmin':
                return redirect()->route('dashboard');
                break;
            case 'admin':
                return redirect()->route('dashboard');
                break;
            case 'moderator':
            case 'moderateur':
                // Le modérateur n'a pas accès au dashboard, rediriger vers les opérations
                return redirect()->route('operations.index');
                break;
            case 'agent':
                return redirect()->route('operations.index');
                break;
            default:
                abort(403, 'Rôle non autorisé');
        }
    }

    /**
     * Afficher la page de test des permissions
     */
    public function permissions()
    {
        return view('test.permissions');
    }

    /**
     * Afficher la structure de connexion
     */
    public function loginStructure()
    {
        return view('test.login-structure');
    }

    /**
     * Vérifier et afficher les permissions de l'utilisateur connecté
     */
    public function checkPermissions()
    {
        $user = auth()->user();

        $permissions = [
            'role' => $user->role,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'modules_accessibles' => $this->getAccessibleModules($user),
            'can_access_parametrage' => in_array($user->role, ['admin', 'superadmin']),
            'is_admin' => in_array($user->role, ['admin', 'superadmin']),
            'is_superadmin' => $user->role === 'superadmin',
        ];

        return response()->json($permissions);
    }

    /**
     * Obtenir la liste des modules accessibles selon le rôle
     */
    private function getAccessibleModules($user)
    {
        // Admin et superadmin ont accès à tous les modules
        if (in_array($user->role, ['admin', 'superadmin'])) {
            return [
                'dashboard' => true,
                'operations' => true,
                'validations' => true,
                'parc' => true,
                'rh' => true,
                'stock' => true,
                'comptabilite' => true,
                'tresorerie' => true,
                'commercial' => true,
                'parametrage' => true,
                'rapports' => true,
            ];
        }

        // Pour les modérateurs : vérifier canAccessModule() pour chaque module
        if (in_array($user->role, ['moderator', 'moderateur'])) {
            $modules = [
                'dashboard' => $user->canAccessModule('dashboard'),
                'operations' => $user->canAccessModule('operations'),
                'validations' => $user->canAccessModule('validations'),
                'parc' => $user->canAccessModule('parc'),
                'rh' => $user->canAccessModule('rh'),
                'stock' => $user->canAccessModule('stock'),
                'comptabilite' => $user->canAccessModule('comptabilite'),
                'tresorerie' => $user->canAccessModule('tresorerie'),
                'commercial' => $user->canAccessModule('commercial'),
                'parametrage' => $user->canAccessModule('parametrage'),
                'rapports' => $user->canAccessModule('rapports'),
            ];
            return array_filter($modules);
        }

        // Cas par défaut (agents, users, etc.)
        $modules = [
            'dashboard' => false,
            'operations' => true,
            'validations' => true,
            'parc' => false,
            'rh' => false,
            'stock' => false,
            'comptabilite' => false,
            'tresorerie' => false,
            'commercial' => false,
            'parametrage' => false,
            'rapports' => false,
        ];

        return array_filter($modules);
    }

    /**
     * Créer des utilisateurs de test pour chaque rôle
     */
    public function createTestUsers()
    {
        $testUsers = [
            [
                'name' => 'Super Admin Test',
                'email' => 'superadmin@kenam.ci',
                'phone' => '0100000001',
                'role' => 'superadmin',
                'password' => 'password123',
            ],
            [
                'name' => 'Admin Test',
                'email' => 'admin@kenam.ci',
                'phone' => '0100000002',
                'role' => 'admin',
                'password' => 'password123',
            ],
            [
                'name' => 'Moderator Test',
                'email' => 'moderator@kenam.ci',
                'phone' => '0100000003',
                'role' => 'moderator',
                'password' => 'password123',
            ],
            [
                'name' => 'Agent Test',
                'email' => 'agent@kenam.ci',
                'phone' => '0100000004',
                'role' => 'agent',
                'password' => 'password123',
            ],
        ];

        $created = [];
        foreach ($testUsers as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'phone' => $userData['phone'],
                    'role' => $userData['role'],
                    'password' => Hash::make($userData['password']),
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
            $created[] = $user->email . ' (' . $user->role . ')';
        }

        return response()->json([
            'message' => 'Utilisateurs de test créés avec succès',
            'users' => $created
        ]);
    }
}
