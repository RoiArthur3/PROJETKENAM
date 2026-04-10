<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class AssignRolesToUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer les rôles de base s'ils n'existent pas
        $roles = [
            'admin',
            'superadmin',
            'tresorerie',
            'commercial',
            'rh',
            'comptabilite',
            'moderator',
            'agent'
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web'
            ]);
        }

        // Assigner le rôle admin au premier utilisateur (s'il existe)
        $adminUser = User::find(1);
        if ($adminUser) {
            $adminRole = Role::where('name', 'admin')->first();
            if ($adminRole && !$adminUser->roles()->where('role_id', $adminRole->id)->exists()) {
                $adminUser->roles()->attach($adminRole->id);
                $this->command->info('Rôle admin assigné à l\'utilisateur ID 1');
            }
        }

        // Assigner le rôle superadmin aux utilisateurs avec email superadmin
        $superAdminUsers = User::where('email', 'like', '%superadmin%')->get();
        foreach ($superAdminUsers as $user) {
            $superAdminRole = Role::where('name', 'superadmin')->first();
            if ($superAdminRole && !$user->roles()->where('role_id', $superAdminRole->id)->exists()) {
                $user->roles()->attach($superAdminRole->id);
                $this->command->info('Rôle superadmin assigné à l\'utilisateur: ' . $user->email);
            }
        }

        // Assigner des rôles par défaut aux autres utilisateurs selon leur email
        $users = User::whereDoesntHave('roles')->get();
        foreach ($users as $user) {
            $roleName = $this->determineUserRole($user->email);
            $role = Role::where('name', $roleName)->first();
            
            if ($role) {
                $user->roles()->attach($role->id);
                $this->command->info('Rôle ' . $roleName . ' assigné à l\'utilisateur: ' . $user->email);
            }
        }

        $this->command->info('Assignation des rôles terminée!');
    }

    /**
     * Déterminer le rôle d'un utilisateur selon son email
     */
    private function determineUserRole(string $email): string
    {
        $email = strtolower($email);
        
        if (strpos($email, 'admin') !== false) {
            return 'admin';
        } elseif (strpos($email, 'tresorerie') !== false || strpos($email, 'finance') !== false) {
            return 'tresorerie';
        } elseif (strpos($email, 'commercial') !== false || strpos($email, 'vente') !== false) {
            return 'commercial';
        } elseif (strpos($email, 'rh') !== false || strpos($email, 'hr') !== false) {
            return 'rh';
        } elseif (strpos($email, 'comptabilite') !== false || strpos($email, 'compta') !== false) {
            return 'comptabilite';
        } elseif (strpos($email, 'moderator') !== false || strpos($email, 'moderateur') !== false) {
            return 'moderator';
        } else {
            return 'agent';
        }
    }
}
