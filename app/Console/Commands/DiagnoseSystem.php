<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Service;

class DiagnoseSystem extends Command
{
    protected $signature = 'system:diagnose {--fix=false}';
    protected $description = 'Diagnostic complet du système KENAM et correction automatique des problèmes';

    public function handle()
    {
        $this->info('🔍 === DIAGNOSTIC COMPLET DU SYSTÈME KENAM ===');
        $this->line('');

        // 1. Vérification de la structure de la base de données
        $this->section('1. STRUCTURE DE LA BASE DE DONNÉES');
        
        // Table users
        $userColumns = Schema::getColumnListing('users');
        $requiredUserColumns = ['id', 'name', 'email', 'role', 'is_active', 'service_id', 'can_access_comptes'];
        
        $this->checkTableStructure('users', $userColumns, $requiredUserColumns);
        
        // Table services
        $serviceColumns = Schema::getColumnListing('services');
        $requiredServiceColumns = ['id', 'nom', 'code'];
        
        $this->checkTableStructure('services', $serviceColumns, $requiredServiceColumns);

        // 2. Vérification des données
        $this->section('2. VÉRIFICATION DES DONNÉES');
        
        $userCount = User::count();
        $serviceCount = Service::count();
        
        $this->info("📊 Utilisateurs: {$userCount}");
        $this->info("📊 Services: {$serviceCount}");
        
        if ($userCount === 0) {
            $this->error('❌ Aucun utilisateur dans la base de données');
            if ($this->option('fix') === 'true') {
                $this->createDefaultUser();
            }
        }
        
        if ($serviceCount === 0) {
            $this->error('❌ Aucun service dans la base de données');
            if ($this->option('fix') === 'true') {
                $this->createDefaultService();
            }
        }

        // 3. Vérification des rôles et permissions
        $this->section('3. VÉRIFICATION DES RÔLES ET PERMISSIONS');
        
        $roles = User::distinct()->pluck('role')->toArray();
        $this->info("👥 Rôles trouvés: " . implode(', ', $roles));
        
        $requiredRoles = ['superadmin', 'admin', 'moderator', 'agent', 'user'];
        $missingRoles = array_diff($requiredRoles, $roles);
        
        if (!empty($missingRoles)) {
            $this->error('❌ Rôles manquants: ' . implode(', ', $missingRoles));
        }

        // 4. Vérification des permissions
        $this->section('4. VÉRIFICATION DES PERMISSIONS');
        
        $usersWithoutPermissions = User::where('can_access_comptes', 0)->get();
        
        if ($usersWithoutPermissions->count() > 0) {
            $this->error("❌ {$usersWithoutPermissions->count()} utilisateurs sans accès aux comptes");
            
            foreach ($usersWithoutPermissions as $user) {
                $this->line("   - {$user->name} ({$user->email}) - Rôle: {$user->role}");
            }
            
            if ($this->option('fix') === 'true') {
                $this->fixPermissions($usersWithoutPermissions);
            }
        }

        // 5. Vérification des routes et contrôleurs
        $this->section('5. VÉRIFICATION DES ROUTES ET CONTRÔLEURS');
        
        $this->checkRoutes();
        $this->checkControllers();
        $this->checkViews();

        // 6. Vérification des relations
        $this->section('6. VÉRIFICATION DES RELATIONS');
        
        $this->checkRelations();

        $this->line('');
        $this->info('✅ Diagnostic terminé !');
        
        if ($this->option('fix') !== 'true') {
            $this->line('');
            $this->info('💡 Pour corriger automatiquement les problèmes, exécutez:');
            $this->comment('   php artisan system:diagnose --fix=true');
        }
    }

    private function section($title)
    {
        $this->line('');
        $this->info($title);
        $this->line(str_repeat('=', strlen($title)));
    }

    private function checkTableStructure($tableName, $actualColumns, $requiredColumns)
    {
        $missingColumns = array_diff($requiredColumns, $actualColumns);
        
        if (empty($missingColumns)) {
            $this->info("✅ Table '{$tableName}' - Structure correcte");
        } else {
            $this->error("❌ Table '{$tableName}' - Colonnes manquantes: " . implode(', ', $missingColumns));
        }
    }

    private function createDefaultUser()
    {
        $this->info('🔧 Création de l utilisateur superadmin par défaut...');
        
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@kenam.com',
            'password' => bcrypt('admin123'),
            'role' => 'superadmin',
            'is_active' => 1,
            'can_access_dashboard' => 1,
            'can_access_operations' => 1,
            'can_access_hr' => 1,
            'can_access_fleet' => 1,
            'can_access_suppliers' => 1,
            'can_access_warehouse' => 1,
            'can_access_accounting' => 1,
            'can_access_invoicing' => 1,
            'can_access_reporting' => 1,
            'can_access_commercial' => 1,
            'can_access_prospection' => 1,
            'can_access_ateliers' => 1,
            'can_access_projects' => 1,
            'can_access_treasury' => 1,
            'can_access_audit' => 1,
            'can_access_services' => 1,
            'can_access_comptes' => 1,
            'can_access_system' => 1,
            'email_verified_at' => now(),
        ]);
        
        $this->info('✅ Utilisateur superadmin créé avec succès');
    }

    private function createDefaultService()
    {
        $this->info('🔧 Création du service par défaut...');
        
        Service::create([
            'nom' => 'Service Administratif',
            'code' => 'ADMIN',
            'email' => 'admin@kenam.com',
            'actif' => 1,
        ]);
        
        $this->info('✅ Service par défaut créé avec succès');
    }

    private function fixPermissions($users)
    {
        $this->info('🔧 Correction des permissions...');
        
        foreach ($users as $user) {
            $user->update(['can_access_comptes' => 1]);
            $this->line("   ✅ {$user->name} - Accès aux comptes activé");
        }
    }

    private function checkRoutes()
    {
        $this->info('🔍 Vérification des routes...');
        
        // Vérifier si les routes principales existent
        $routes = [
            'admin.comptes.users.index',
            'admin.comptes.users.create',
            'admin.comptes.users.store',
            'admin.comptes.users.edit',
            'admin.comptes.users.update',
            'admin.comptes.users.destroy',
        ];
        
        foreach ($routes as $routeName) {
            try {
                route($routeName);
                $this->info("   ✅ Route '{$routeName}' existe");
            } catch (\Exception $e) {
                $this->error("   ❌ Route '{$routeName}' manquante");
            }
        }
    }

    private function checkControllers()
    {
        $this->info('🔍 Vérification des contrôleurs...');
        
        $controllers = [
            'App\Http\Controllers\CompteController',
        ];
        
        foreach ($controllers as $controller) {
            if (class_exists($controller)) {
                $this->info("   ✅ Contrôleur '{$controller}' existe");
            } else {
                $this->error("   ❌ Contrôleur '{$controller}' manquant");
            }
        }
    }

    private function checkViews()
    {
        $this->info('🔍 Vérification des vues...');
        
        $views = [
            'admin.comptes.users.index',
            'admin.comptes.users.create',
            'admin.comptes.users.edit',
            'admin.comptes.users.show',
        ];
        
        foreach ($views as $view) {
            try {
                view($view);
                $this->info("   ✅ Vue '{$view}' existe");
            } catch (\Exception $e) {
                $this->error("   ❌ Vue '{$view}' manquante");
            }
        }
    }

    private function checkRelations()
    {
        $this->info('🔍 Vérification des relations...');
        
        // Vérifier la relation users -> services
        $usersWithInvalidService = User::whereNotNull('service_id')
            ->whereNotIn('service_id', Service::pluck('id'))
            ->get();
        
        if ($usersWithInvalidService->count() > 0) {
            $this->error("❌ {$usersWithInvalidService->count()} utilisateurs avec service_id invalide");
            
            foreach ($usersWithInvalidService as $user) {
                $this->line("   - {$user->name} (service_id: {$user->service_id})");
            }
        } else {
            $this->info("   ✅ Toutes les relations users->services sont valides");
        }
    }
}
