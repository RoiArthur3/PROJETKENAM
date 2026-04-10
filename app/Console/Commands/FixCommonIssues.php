<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class FixCommonIssues extends Command
{
    protected $signature = 'system:fix-common-issues';
    protected $description = 'Correction automatique des problèmes courants du système KENAM';

    public function handle()
    {
        $this->info('🔧 === CORRECTION DES PROBLÈMES COURANTS ===');
        $this->line('');

        $fixes = [];

        // 1. Corriger les imports dans CompteController
        $this->fixControllerImports($fixes);

        // 2. Corriger les hasRole dans les vues
        $this->fixViewRoleChecks($fixes);

        // 3. Corriger les erreurs de syntaxe dans les routes
        $this->fixRouteSyntax($fixes);

        // 4. Corriger les permissions par défaut
        $this->fixDefaultPermissions($fixes);

        // 5. Vider les caches
        $this->clearCaches($fixes);

        $this->displayResults($fixes);
    }

    private function fixControllerImports(&$fixes)
    {
        $this->info('🔍 Correction des imports dans CompteController...');

        $controllerFile = base_path('app/Http/Controllers/CompteController.php');

        if (!File::exists($controllerFile)) {
            $this->error('❌ CompteController.php introuvable');
            return;
        }

        $content = File::get($controllerFile);

        // Remplacer l'import incorrect
        if (str_contains($content, 'use Illuminate\Foundation\Auth\User')) {
            $content = str_replace(
                'use Illuminate\Foundation\Auth\User as Authenticatable;',
                'use App\Models\User;',
                $content
            );

            // Remplacer toutes les occurrences de Authenticatable:: par User::
            $content = str_replace('Authenticatable::', 'User::', $content);

            // Remplacer les paramètres de fonction
            $content = preg_replace('/function\s+\w+\s*\(\s*Authenticatable\s+\$\w+\s*\)/', 'function $1(User $$2)', $content);

            File::put($controllerFile, $content);
            $fixes[] = '✅ Imports CompteController corrigés';
        } else {
            $fixes[] = 'ℹ️  Imports CompteController déjà corrects';
        }
    }

    private function fixViewRoleChecks(&$fixes)
    {
        $this->info('🔍 Correction des vérifications de rôle dans les vues...');

        $viewFile = base_path('resources/views/admin/comptes/users/index.blade.php');

        if (!File::exists($viewFile)) {
            $this->error('❌ Vue index.blade.php introuvable');
            return;
        }

        $content = File::get($viewFile);

        // Remplacer les hasRole par des vérifications directes
        $patterns = [
            '/!\$user->hasRole\([\'"]admin[\'"]\)\s*&&\s*!\$user->hasRole\([\'"]superadmin[\'"]\)/' => '$user->role !== \'admin\' && $user->role !== \'superadmin\'',
            '/\$user->hasRole\([\'"]admin[\'"]\)/' => '$user->role === \'admin\'',
            '/\$user->hasRole\([\'"]superadmin[\'"]\)/' => '$user->role === \'superadmin\'',
        ];

        $changed = false;
        foreach ($patterns as $pattern => $replacement) {
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, $replacement, $content);
                $changed = true;
            }
        }

        if ($changed) {
            File::put($viewFile, $content);
            $fixes[] = '✅ Vérifications de rôle dans la vue corrigées';
        } else {
            $fixes[] = 'ℹ️  Vérifications de rôle dans la vue déjà correctes';
        }
    }

    private function fixRouteSyntax(&$fixes)
    {
        $this->info('🔍 Correction de la syntaxe des routes...');

        $routesFile = base_path('routes/web.php');

        if (!File::exists($routesFile)) {
            $this->error('❌ routes/web.php introuvable');
            return;
        }

        $content = File::get($routesFile);

        // Vérifier les accolades
        $openBraces = substr_count($content, '{');
        $closeBraces = substr_count($content, '}');

        if ($openBraces !== $closeBraces) {
            $difference = $openBraces - $closeBraces;

            if ($difference > 0) {
                // Ajouter les accolades manquantes à la fin
                $content .= str_repeat('}', $difference);
                File::put($routesFile, $content);
                $fixes[] = "✅ {$difference} accolade(s) fermante(s) ajoutée(s)";
            } else {
                $fixes[] = "⚠️  Trop d'accolades fermantes (différence: " . abs($difference) . ")";
            }
        } else {
            $fixes[] = 'ℹ️  Syntaxe des routes déjà correcte';
        }
    }

    private function fixDefaultPermissions(&$fixes)
    {
        $this->info('🔍 Correction des permissions par défaut...');

        try {
            $users = \App\Models\User::where(function ($q) {
                $q->whereNull('can_access_comptes')
                    ->orWhere('can_access_comptes', 0);
            })->get();

            if ($users->count() > 0) {
                foreach ($users as $user) {
                    $user->can_access_comptes = 1;
                    $user->save();
                }
                $fixes[] = "✅ Permissions d'accès aux comptes activées pour {$users->count()} utilisateur(s)";
            } else {
                $fixes[] = 'ℹ️  Permissions déjà correctes pour tous les utilisateurs';
            }
        } catch (\Exception $e) {
            $fixes[] = "❌ Erreur lors de la correction des permissions: " . $e->getMessage();
        }
    }

    private function clearCaches(&$fixes)
    {
        $this->info('🔍 Vidage des caches...');

        try {
            Artisan::call('route:clear');
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('view:clear');

            $fixes[] = '✅ Tous les caches vidés avec succès';
        } catch (\Exception $e) {
            $fixes[] = "❌ Erreur lors du vidage des caches: " . $e->getMessage();
        }
    }

    private function displayResults($fixes)
    {
        $this->line('');
        $this->info('📊 RÉSULTATS DES CORRECTIONS');
        $this->line(str_repeat('=', 50));

        foreach ($fixes as $fix) {
            $this->line("   {$fix}");
        }

        $this->line('');
        $this->info('🎉 Corrections terminées !');
        $this->comment('💡 Exécutez maintenant: php artisan system:health-check pour vérifier');
    }
}
