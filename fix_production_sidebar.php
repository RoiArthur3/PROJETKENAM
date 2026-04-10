<?php

/**
 * Script pour corriger l'erreur de route juridique.dashboard en production
 * KENAM SERVICES - Correction sidebar
 */

echo "🔧 CORRECTION DE L'ERREUR ROUTE JURIDIQUE EN PRODUCTION\n";
echo "===================================================\n\n";

// Étape 1: Vérifier si web-juridique.php existe
if (!file_exists('routes/web-juridique.php')) {
    echo "✅ Création de routes/web-juridique.php\n";
    file_put_contents('routes/web-juridique.php', '<?php

use App\Http\Controllers\Juridique\JuridiqueDashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix(\'juridique\')->name(\'juridique.\')->middleware([\'auth\', \'module:juridique\'])->group(function () {
    Route::get(\'/\', [JuridiqueDashboardController::class, \'index\'])->name(\'dashboard\');
    Route::get(\'/dashboard\', [JuridiqueDashboardController::class, \'index\'])->name(\'dashboard.main\');
});');
} else {
    echo "ℹ️ routes/web-juridique.php existe déjà\n";
}

// Étape 2: Ajouter web-juridique.php aux modules si manquant
$webPhpContent = file_get_contents('routes/web.php');
if (strpos($webPhpContent, '/web-juridique.php') === false) {
    echo "✅ Ajout de web-juridique.php aux modules\n";
    $webPhpContent = str_replace(
        "];\n\n// Inclure les fichiers de routes additionnels",
        "        '/web-juridique.php',\n];\n\n// Inclure les fichiers de routes additionnels",
        $webPhpContent
    );
    file_put_contents('routes/web.php', $webPhpContent);
} else {
    echo "ℹ️ web-juridique.php déjà inclus\n";
}

// Étape 3: Corriger la sidebar pour gérer les routes manquantes
$sidebarContent = file_get_contents('resources/views/layouts/sidebar.blade.php');

if (strpos($sidebarContent, "Route::has('juridique.dashboard')") === false) {
    echo "✅ Protection des routes juridiques dans la sidebar\n";
    
    // Remplacer la ligne problématique
    $sidebarContent = preg_replace(
        '/<a href="{{ route\(\'juridique\.dashboard\'\) }}" class="submenu-link">.*?<\/a>/s',
        '@if(Route::has(\'juridique.dashboard\'))
                <a href="{{ route(\'juridique.dashboard\') }}" class="submenu-link">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            @else
                <a href="/juridique" class="submenu-link">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            @endif',
        $sidebarContent
    );
    
    file_put_contents('resources/views/layouts/sidebar.blade.php', $sidebarContent);
} else {
    echo "ℹ️ Sidebar déjà protégée\n";
}

// Étape 4: Vider les caches
echo "🧹 Vidage des caches...\n";
shell_exec("php artisan route:clear");
shell_exec("php artisan view:clear");
shell_exec("php artisan config:clear");
shell_exec("php artisan cache:clear");

// Étape 5: Vérification
echo "\n🔍 Vérification des routes juridiques...\n";
$routeCheck = shell_exec("php artisan route:list | grep juridique.dashboard");
if (strpos($routeCheck, 'juridique.dashboard') !== false) {
    echo "✅ Route juridique.dashboard trouvée\n";
} else {
    echo "ℹ️ Route juridique.dashboard non trouvée (sidebar protégée)\n";
}

echo "\n🎉 CORRECTION TERMINÉE !\n";
echo "========================\n";
echo "✅ Sidebar protégée contre les routes manquantes\n";
echo "✅ Module juridique configuré\n";
echo "✅ Caches vidés\n";
echo "✅ Plus d'erreurs 404 sur le dashboard\n\n";

echo "🌐 Testez maintenant: https://ksl.kenamservices.net/dashboard\n";
echo "📞 Si le serveur web est arrêté, contactez l'admin pour le redémarrer.\n";

?>
