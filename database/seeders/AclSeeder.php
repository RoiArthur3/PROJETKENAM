<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AclSeeder extends Seeder
{
    public function run(): void
    {
        // Nettoyer les tables si elles existent
        DB::table('role_user')->delete();
        DB::table('roles')->delete();

        // Créer les rôles simples
        $roles = [
            ['id' => 1, 'name' => 'superadmin', 'guard_name' => 'web'],
            ['id' => 2, 'name' => 'admin', 'guard_name' => 'web'],
            ['id' => 3, 'name' => 'moderation', 'guard_name' => 'web'],
            ['id' => 4, 'name' => 'agent', 'guard_name' => 'web'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert($role);
        }

        // Créer des utilisateurs de test avec rôles
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@kenamservices.net',
                'password' => bcrypt('admin123'),
                'role' => 'superadmin',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Admin Test',
                'email' => 'admin@test.com',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            $userId = DB::table('users')->insertGetId($user);
            // Associer l'utilisateur à son rôle
            $roleId = DB::table('roles')->where('name', $user['role'])->value('id');
            if ($roleId) {
                DB::table('role_user')->insert([
                    'user_id' => $userId,
                    'role_id' => $roleId,
                ]);
            }
        }

        $this->command?->info('ACL simplifié créé : rôles et utilisateurs de test.');
    }
}
