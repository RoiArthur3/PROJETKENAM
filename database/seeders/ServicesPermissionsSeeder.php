<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class ServicesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Permissions pour les services opérationnels
        $permissions = [
            'services.view',
            'services.create',
            'services.edit',
            'services.delete',
            'services.email',
            'services.reset',
        ];

        // Créer les permissions si elles n'existent pas
        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web'
            ]);
        }

        // Donner toutes les permissions aux rôles admin et superadmin
        $adminRoles = Role::whereIn('name', ['admin', 'superadmin'])->get();

        foreach ($adminRoles as $role) {
            foreach ($permissions as $permissionName) {
                $permission = Permission::where('name', $permissionName)->first();
                if ($permission && !$role->permissions()->where('permission_id', $permission->id)->exists()) {
                    $role->permissions()->attach($permission->id);
                }
            }
        }

        $this->command->info('Permissions des services opérationnels créées avec succès!');
    }

    /**
     * Obtenir la description d'une permission
     */
    private function getPermissionDescription(string $permission): string
    {
        $descriptions = [
            'services.view' => 'Voir la liste des services opérationnels',
            'services.create' => 'Créer un nouveau service opérationnel',
            'services.edit' => 'Modifier un service opérationnel',
            'services.delete' => 'Supprimer un service opérationnel',
            'services.email' => 'Tester les emails des services',
            'services.reset' => 'Réinitialiser les mots de passe des services',
        ];

        return $descriptions[$permission] ?? 'Permission pour les services opérationnels';
    }
}
