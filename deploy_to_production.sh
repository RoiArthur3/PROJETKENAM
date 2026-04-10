#!/bin/bash

# SCRIPT DE DÉPLOIEMENT PROFESSIONNEL POUR KENAM SERVICES
# Correction complète des routes en production

echo "🚀 DÉPLOIEMENT PROFESSIONNEL SUR PRODUCTION"
echo "=========================================="
echo "Serveur: ksl.kenamservices.net"
echo "Date: $(date)"
echo ""

cd /home/seke9323/ksl.kenamservices.net

echo "📋 ÉTAPE 1: Création du fichier de correction des routes..."
cat > fix_production_routes.php << 'EOF'
<?php

echo "🚀 CORRECTION PROFESSIONNELLE DES ROUTES - PRODUCTION\n";
echo "===================================================\n\n";

// Étape 1: Créer le fichier web-juridique.php manquant
if (!file_exists('routes/web-juridique.php')) {
    echo "✅ Création de routes/web-juridique.php\n";
    file_put_contents('routes/web-juridique.php', '<?php

use App\Http\Controllers\Juridique\ContratController;
use App\Http\Controllers\Juridique\JuridiqueDashboardController;
use App\Http\Controllers\Juridique\DocumentController;
use App\Http\Controllers\Juridique\EcheanceController;
use App\Http\Controllers\Juridique\FinancementController;
use App\Http\Controllers\Juridique\OffreBancaireController;
use Illuminate\Support\Facades\Route;

Route::prefix(\'juridique\')->name(\'juridique.\')->middleware([\'auth\', \'module:juridique\'])->group(function () {
    Route::get(\'/\', [JuridiqueDashboardController::class, \'index\'])->name(\'dashboard\');
    Route::get(\'/dashboard\', [JuridiqueDashboardController::class, \'index\'])->name(\'dashboard.main\');

    Route::resource(\'contrats\', ContratController::class);
    Route::get(\'contrats/{contrat}/duplicate\', [ContratController::class, \'duplicate\'])->name(\'contrats.duplicate\');
    Route::post(\'contrats/{contrat}/creer-financement\', [ContratController::class, \'creer-financement\'])->name(\'contrats.creer-financement\');
    Route::get(\'contrats/export\', [ContratController::class, \'export\'])->name(\'contrats.export\');

    Route::resource(\'documents\', DocumentController::class);
    Route::resource(\'financements\', FinancementController::class);
    Route::post(\'offres/{offre}/generate-echeancier\', [OffreBancaireController::class, \'generateEcheancier\'])->name(\'offres.generate-echeancier\');
    Route::resource(\'offres\', OffreBancaireController::class)->parameters([\'offres\' => \'offre\']);
});');
} else {
    echo "ℹ️ routes/web-juridique.php existe déjà\n";
}

// Étape 2: Ajouter web-juridique.php aux modules inclus
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

// Étape 3: Corriger les conflits API
$apiPhpContent = file_get_contents('routes/api.php');
if (strpos($apiPhpContent, 'api.types-operations') === false) {
    echo "✅ Correction des conflits types-operations\n";
    $apiPhpContent = str_replace(
        "Route::apiResource('types-operations', TypeOperationController::class);",
        "Route::apiResource('types-operations', TypeOperationController::class)->names([
            'index' => 'api.types-operations.index',
            'store' => 'api.types-operations.store',
            'show' => 'api.types-operations.show',
            'update' => 'api.types-operations.update',
            'destroy' => 'api.types-operations.destroy'
        ]);",
        $apiPhpContent
    );
    file_put_contents('routes/api.php', $apiPhpContent);
} else {
    echo "ℹ️ Conflits types-operations déjà corrigés\n";
}

echo "\n🧹 Vidage des caches...\n";
shell_exec("php artisan route:clear");
shell_exec("php artisan config:clear");
shell_exec("php artisan view:clear");
shell_exec("php artisan cache:clear");

echo "\n🔍 Vérification des routes critiques...\n";
$dashboardCheck = shell_exec("php artisan route:list | grep 'dashboard.*DashboardController@index'");
$juridiqueCheck = shell_exec("php artisan route:list | grep 'juridique.dashboard'");

if (strpos($dashboardCheck, 'dashboard') !== false) {
    echo "✅ Route dashboard trouvée\n";
} else {
    echo "❌ Route dashboard manquante\n";
}

if (strpos($juridiqueCheck, 'juridique.dashboard') !== false) {
    echo "✅ Route juridique.dashboard trouvée\n";
} else {
    echo "❌ Route juridique.dashboard manquante\n";
}

echo "\n🎉 CORRECTION TERMINÉE AVEC SUCCÈS !\n";
echo "===================================\n";
?>
EOF

echo "📋 ÉTAPE 2: Exécution de la correction..."
php fix_production_routes.php

echo ""
echo "📋 ÉTAPE 2B: Activation des fichiers uploades en ligne..."

if [ -L public/storage ]; then
    echo "ℹ️ Lien symbolique public/storage deja present"
elif [ -e public/storage ]; then
    echo "⚠️ public/storage existe mais n'est pas un lien symbolique"
    echo "   Supprimez ce dossier/fichier puis relancez le script pour activer le lien storage."
else
    php artisan storage:link && echo "✅ Lien storage cree" || echo "❌ Echec creation lien storage"
fi

if grep -q '^APP_URL=' .env; then
    echo "ℹ️ APP_URL configure: $(grep '^APP_URL=' .env | head -1)"
else
    echo "⚠️ APP_URL absent dans .env (recommande en production)"
fi

if grep -q '^FILESYSTEM_DISK=' .env; then
    echo "ℹ️ FILESYSTEM_DISK configure: $(grep '^FILESYSTEM_DISK=' .env | head -1)"
else
    echo "⚠️ FILESYSTEM_DISK absent dans .env (recommande: FILESYSTEM_DISK=public)"
fi

echo "🧹 Rechargement de la configuration..."
php artisan config:clear
php artisan cache:clear

echo ""
echo "📋 ÉTAPE 3: Vérification finale des routes..."
echo "Routes dashboard:"
php artisan route:list | grep "dashboard.*DashboardController@index"
echo ""
echo "Routes juridique:"
php artisan route:list | grep juridique.dashboard

echo ""
echo "📋 ÉTAPE 4: Nettoyage..."
rm fix_production_routes.php

echo ""
echo "✅ DÉPLOIEMENT TERMINÉ !"
echo ""
echo "🌐 Testez maintenant:"
echo "   - Dashboard: https://ksl.kenamservices.net/dashboard"
echo "   - Juridique: https://ksl.kenamservices.net/juridique"
echo "   - Uploads:   https://ksl.kenamservices.net/storage/..."
echo ""
echo "📞 Si le serveur web est arrêté, contactez l'admin:"
echo "   'URGENT: Redémarrage du serveur web requis sur ksl.kenamservices.net'"
echo ""
