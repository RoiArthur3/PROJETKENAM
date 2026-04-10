<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserPermissionTestController extends Controller
{
    public function index()
    {
        $users = User::orderBy('role')->get();
        
        return view('test.permissions.index', compact('users'));
    }

    public function createTestUsers()
    {
        $testUsers = [
            [
                'name' => 'Admin Test',
                'email' => 'admin@test.com',
                'username' => 'admin_test',
                'password' => 'password123',
                'role' => 'admin',
                'permissions' => null, // Admin a tout accès
            ],
            [
                'name' => 'Chauffeur Test',
                'email' => 'chauffeur@test.com',
                'username' => 'chauffeur_test',
                'password' => 'password123',
                'role' => 'chauffeur',
                'permissions' => [
                    'operations' => [
                        'submenus' => ['execution', 'tracking']
                    ]
                ],
            ],
            [
                'name' => 'Commercial Test',
                'email' => 'commercial@test.com',
                'username' => 'commercial_test',
                'password' => 'password123',
                'role' => 'commercial',
                'permissions' => null,
            ],
            [
                'name' => 'Comptable Test',
                'email' => 'comptable@test.com',
                'username' => 'comptable_test',
                'password' => 'password123',
                'role' => 'comptable',
                'permissions' => null,
            ],
            [
                'name' => 'RH Test',
                'email' => 'rh@test.com',
                'username' => 'rh_test',
                'password' => 'password123',
                'role' => 'rh',
                'permissions' => null,
            ],
            [
                'name' => 'Technicien Parc Auto',
                'email' => 'technicien@test.com',
                'username' => 'technicien_test',
                'password' => 'password123',
                'role' => 'technicien',
                'permissions' => null,
            ],
        ];

        $createdUsers = [];
        $updatedUsers = [];

        foreach ($testUsers as $userData) {
            $user = User::where('email', $userData['email'])->first();
            
            if ($user) {
                // Update existing user
                $user->update([
                    'name' => $userData['name'],
                    'username' => $userData['username'],
                    'role' => $userData['role'],
                    'permissions' => $userData['permissions'],
                    'is_active' => true,
                ]);
                $updatedUsers[] = $user->email;
            } else {
                // Create new user
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'username' => $userData['username'],
                    'password' => Hash::make($userData['password']),
                    'role' => $userData['role'],
                    'permissions' => $userData['permissions'],
                    'is_active' => true,
                ]);
                $createdUsers[] = $user->email;
            }

            // Set module permissions based on role
            $this->setUserModulePermissions($user, $userData['role']);
        }

        return redirect()->back()->with('success', 
            "Utilisateurs créés: " . implode(', ', $createdUsers) . 
            " | Utilisateurs mis à jour: " . implode(', ', $updatedUsers)
        );
    }

    private function setUserModulePermissions(User $user, string $role)
    {
        $permissions = [
            'admin' => [
                'can_access_dashboard' => true,
                'can_access_operations' => true,
                'can_access_fleet' => true,
                'can_access_hr' => true,
                'can_access_suppliers' => true,
                'can_access_warehouse' => true,
                'can_access_accounting' => true,
                'can_access_invoicing' => true,
                'can_access_reporting' => true,
                'can_access_commercial' => true,
                'can_access_prospection' => true,
                'can_access_ateliers' => true,
            ],
            'chauffeur' => [
                'can_access_dashboard' => true,
                'can_access_operations' => true,
                'can_access_fleet' => false,
                'can_access_hr' => false,
                'can_access_suppliers' => false,
                'can_access_warehouse' => false,
                'can_access_accounting' => false,
                'can_access_invoicing' => false,
                'can_access_reporting' => false,
                'can_access_commercial' => false,
                'can_access_prospection' => false,
                'can_access_ateliers' => false,
            ],
            'commercial' => [
                'can_access_dashboard' => true,
                'can_access_operations' => false,
                'can_access_fleet' => false,
                'can_access_hr' => false,
                'can_access_suppliers' => true,
                'can_access_warehouse' => false,
                'can_access_accounting' => false,
                'can_access_invoicing' => true,
                'can_access_reporting' => true,
                'can_access_commercial' => true,
                'can_access_prospection' => true,
                'can_access_ateliers' => false,
            ],
            'comptable' => [
                'can_access_dashboard' => true,
                'can_access_operations' => false,
                'can_access_fleet' => false,
                'can_access_hr' => false,
                'can_access_suppliers' => true,
                'can_access_warehouse' => false,
                'can_access_accounting' => true,
                'can_access_invoicing' => true,
                'can_access_reporting' => true,
                'can_access_commercial' => false,
                'can_access_prospection' => false,
                'can_access_ateliers' => false,
            ],
            'rh' => [
                'can_access_dashboard' => true,
                'can_access_operations' => false,
                'can_access_fleet' => false,
                'can_access_hr' => true,
                'can_access_suppliers' => false,
                'can_access_warehouse' => false,
                'can_access_accounting' => false,
                'can_access_invoicing' => false,
                'can_access_reporting' => true,
                'can_access_commercial' => false,
                'can_access_prospection' => false,
                'can_access_ateliers' => false,
            ],
            'technicien' => [
                'can_access_dashboard' => true,
                'can_access_operations' => false,
                'can_access_fleet' => true,
                'can_access_hr' => false,
                'can_access_suppliers' => false,
                'can_access_warehouse' => false,
                'can_access_accounting' => false,
                'can_access_invoicing' => false,
                'can_access_reporting' => false,
                'can_access_commercial' => false,
                'can_access_prospection' => false,
                'can_access_ateliers' => true,
            ],
        ];

        $user->update($permissions[$role] ?? []);
    }

    public function testUser(User $user)
    {
        // Simuler la connexion de l'utilisateur pour tester
        $accessibleModules = $user->getAccessibleModules();
        $primaryModule = $user->getPrimaryModule();
        
        return view('test.permissions.test', compact('user', 'accessibleModules', 'primaryModule'));
    }
}
