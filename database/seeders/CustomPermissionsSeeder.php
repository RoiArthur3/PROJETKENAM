<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CustomPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Créer les rôles de base
        $rolePayloadSuperadmin = ['name' => 'superadmin', 'guard_name' => 'web'];
        $rolePayloadStockAdmin = ['name' => 'stock-admin', 'guard_name' => 'web'];

        if (Schema::hasColumn('roles', 'description')) {
            $rolePayloadSuperadmin['description'] = 'Super Administrateur avec tous les accès';
            $rolePayloadStockAdmin['description'] = 'Administrateur du module Stock';
        }

        $superadmin = Role::firstOrCreate(['name' => 'superadmin'], $rolePayloadSuperadmin);
        $stockAdmin = Role::firstOrCreate(['name' => 'stock-admin'], $rolePayloadStockAdmin);

        // Créer les permissions pour le module Stock
        $permissions = [
            ['name' => 'module.stock', 'guard_name' => 'web'],
            ['name' => 'stock.view', 'guard_name' => 'web'],
            ['name' => 'stock.products.manage', 'guard_name' => 'web'],
            ['name' => 'stock.warehouses.manage', 'guard_name' => 'web'],
            ['name' => 'stock.movements.manage', 'guard_name' => 'web']
        ];

        foreach ($permissions as $permissionData) {
            $permission = Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                $permissionData
            );

            $stockAdmin->permissions()->syncWithoutDetaching([$permission->id]);
            $superadmin->permissions()->syncWithoutDetaching([$permission->id]);
        }
    }
}
