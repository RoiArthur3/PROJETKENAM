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
                return redirect()->route('operations.dashboard');
                break;
            case 'moderator':
                return redirect()->route('operations.dashboard');
                break;
            case 'agent':
                return redirect()->route('operations.dashboard');
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
        $modules = [
            'dashboard' => $user->role === 'superadmin',
            'operations' => true, // Tous les rôles ont accès aux opérations
            'validations' => true, // Tous les rôles ont accès aux validations
            'parc' => in_array($user->role, ['admin', 'superadmin']),
            'rh' => in_array($user->role, ['admin', 'superadmin']),
            'stock' => in_array($user->role, ['admin', 'superadmin']),
            'comptabilite' => in_array($user->role, ['admin', 'superadmin']),
            'tresorerie' => in_array($user->role, ['admin', 'superadmin']),
            'commercial' => in_array($user->role, ['admin', 'superadmin']),
            'parametrage' => in_array($user->role, ['admin', 'superadmin']),
            'rapports' => in_array($user->role, ['admin', 'superadmin']),
        ];

        // Pour les modérateurs, vérifier les permissions spécifiques
        if ($user->role === 'moderator' || $user->role === 'moderateur') {
            // Ajouter les modules spécifiques au modérateur selon ses permissions
            $modules['parc'] = $this->hasModulePermission($user, 'parc');
            $modules['rh'] = $this->hasModulePermission($user, 'rh');
            $modules['stock'] = $this->hasModulePermission($user, 'stock');
            $modules['comptabilite'] = $this->hasModulePermission($user, 'comptabilite');
            $modules['tresorerie'] = $this->hasModulePermission($user, 'tresorerie');
            $modules['commercial'] = $this->hasModulePermission($user, 'commercial');
        }

        return array_filter($modules);
    }

    /**
     * Vérifier si l'utilisateur a une permission de module spécifique
     */
    private function hasModulePermission($user, $module)
    {
        // Vérifier les permissions dans la base de données
        $permissionField = 'can_access_' . $module;
        return isset($user->$permissionField) && $user->$permissionField == true;
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
