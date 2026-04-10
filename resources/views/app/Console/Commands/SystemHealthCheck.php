<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

class SystemHealthCheck extends Command
{
    protected $signature = 'system:health-check';
    protected $description = 'Vérification rapide de la santé du système KENAM';

    public function handle()
    {
        $this->info('🏥 === CHECK-UP SANTÉ SYSTÈME KENAM ===');
        $this->line('');

        $issues = [];
        $warnings = [];

        // 1. Vérification des fichiers critiques
        $this->checkCriticalFiles($issues, $warnings);

        // 2. Vérification des routes
        $this->checkRoutes($issues, $warnings);

        // 3. Vérification des permissions
        $this->checkPermissions($issues, $warnings);

        // 4. Vérification des erreurs courantes
        $this->checkCommonErrors($issues, $warnings);

        // 5. Vérification des dépendances
        $this->checkDependencies($issues, $warnings);

        // Résultat
        $this->displayResults($issues, $warnings);

        return count($issues) === 0 ? 0 : 1;
    }

    private function checkCriticalFiles(&$issues, &$warnings)
    {
        $this->info('📁 Vérification des fichiers critiques...');

        $criticalFiles = [
            'routes/web.php' => 'Fichier de routes principal',
            'app/Http/Controllers/CompteController.php' => 'Contrôleur des comptes',
            'app/Models/User.php' => 'Modèle User',
            'app/Models/Service.php' => 'Modèle Service',
            'resources/views/admin/comptes/users/index.blade.php' => 'Vue index utilisateurs',
            'app/Http/Kernel.php' => 'Kernel HTTP',
        ];

        foreach ($criticalFiles as $file => $description) {
            if (!File::exists(base_path($file))) {
                $issues[] = "❌ Fichier manquant: {$file} ({$description})";
            } else {
                $this->line("   ✅ {$description}");
            }
        }
    }

    private function checkRoutes(&$issues, &$warnings)
    {
        $this->info('🛣️  Vérification des routes...');

        try {
            $routes = Route::getRoutes();
            $userRoutes = [];

            foreach ($routes as $route) {
                if (str_contains($route->uri(), 'admin/comptes/users')) {
                    $userRoutes[] = $route->methods()[0] . ' ' . $route->uri();
                }
            }

            if (empty($userRoutes)) {
                $issues[] = "❌ Aucune route trouvée pour admin/comptes/users";
            } else {
                $this->line("   ✅ " . count($userRoutes) . " routes trouvées pour admin/comptes/users");

                foreach ($userRoutes as $route) {
                    $this->line("      - {$route}");
                }
            }
        } catch (\Exception $e) {
            $issues[] = "❌ Erreur lors de la vérification des routes: " . $e->getMessage();
        }
    }

    private function checkPermissions(&$issues, &$warnings)
    {
        $this->info('🔐 Vérification des permissions...');

        try {
            $users = \App\Models\User::get();

            foreach ($users as $user) {
                if ($user->role === 'superadmin' || $user->role === 'admin') {
                    if (!$user->can_access_comptes) {
                        $warnings[] = "⚠️  L'utilisateur {$user->name} ({$user->role}) n'a pas accès aux comptes";
                    }
                }
            }

            $this->line("   ✅ Permissions vérifiées pour " . $users->count() . " utilisateurs");
        } catch (\Exception $e) {
            $issues[] = "❌ Erreur lors de la vérification des permissions: " . $e->getMessage();
        }
    }

    private function checkCommonErrors(&$issues, &$warnings)
    {
        $this->info('🔍 Vérification des erreurs courantes...');

        // Vérifier les erreurs de syntaxe dans routes/web.php
        $routesFile = base_path('routes/web.php');
        if (File::exists($routesFile)) {
            $content = File::get($routesFile);

            // Vérifier les accolades non fermées
            $openBraces = substr_count($content, '{');
            $closeBraces = substr_count($content, '}');

            if ($openBraces !== $closeBraces) {
                $issues[] = "❌ Erreur de syntaxe dans routes/web.php: {$openBraces} '{' contre {$closeBraces} '}'";
            } else {
                $this->line("   ✅ Syntaxe routes/web.php correcte");
            }
        }

        // Vérifier les imports incorrects
        $controllerFile = base_path('app/Http/Controllers/CompteController.php');
        if (File::exists($controllerFile)) {
            $content = File::get($controllerFile);

            if (str_contains($content, 'use Illuminate\Foundation\Auth\User')) {
                $issues[] = "❌ Import incorrect dans CompteController: utiliser 'use App\Models\User'";
            } else {
                $this->line("   ✅ Imports CompteController corrects");
            }
        }

        // Vérifier les méthodes hasRole dans les vues
        $viewFile = base_path('resources/views/admin/comptes/users/index.blade.php');
        if (File::exists($viewFile)) {
            $content = File::get($viewFile);

            if (str_contains($content, 'hasRole(')) {
                $warnings[] = "⚠️  Utilisation de hasRole() détecté dans la vue - utiliser \$user->role à la place";
            } else {
                $this->line("   ✅ Vue index utilisateurs correcte");
            }
        }
    }

    private function checkDependencies(&$issues, &$warnings)
    {
        $this->info('📦 Vérification des dépendances...');

        // Vérifier si les modèles existent
        if (!class_exists('App\Models\User')) {
            $issues[] = "❌ Modèle App\Models\User introuvable";
        } else {
            $this->line("   ✅ Modèle User disponible");
        }

        if (!class_exists('App\Models\Service')) {
            $issues[] = "❌ Modèle App\Models\Service introuvable";
        } else {
            $this->line("   ✅ Modèle Service disponible");
        }

        // Vérifier si les contrôleurs existent
        if (!class_exists('App\Http\Controllers\CompteController')) {
            $issues[] = "❌ Contrôleur CompteController introuvable";
        } else {
            $this->line("   ✅ Contrôleur CompteController disponible");
        }
    }

    private function displayResults($issues, $warnings)
    {
        $this->line('');
        $this->info('📊 RÉSULTATS DU CHECK-UP');
        $this->line(str_repeat('=', 50));

        if (empty($issues) && empty($warnings)) {
            $this->info('🎉 Tout est parfait ! Le système est en bonne santé.');
            return;
        }

        if (!empty($issues)) {
            $this->error('❌ PROBLÈMES CRITIQUES (' . count($issues) . ')');
            foreach ($issues as $issue) {
                $this->line("   {$issue}");
            }
        }

        if (!empty($warnings)) {
            $this->warn('⚠️  AVERTISSEMENTS (' . count($warnings) . ')');
            foreach ($warnings as $warning) {
                $this->line("   {$warning}");
            }
        }

        $this->line('');
        $this->info('💡 RECOMMANDATIONS:');

        if (!empty($issues)) {
            $this->comment('   - Exécutez: php artisan system:diagnose --fix=true');
            $this->comment('   - Vérifiez les logs: tail -f storage/logs/laravel.log');
        }

        if (!empty($warnings)) {
            $this->comment('   - Corrigez les avertissements pour améliorer la stabilité');
        }
    }
}
