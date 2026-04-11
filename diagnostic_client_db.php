<?php
echo "=== DIAGNOSTIC BASE DE DONNÉES - CLIENT KSL ===\n\n";

// Configuration du client
$host = 'localhost';
$port = 3306;
$database = 'seke9323_kenam';
$username = 'seke9323_ksl';
$password = 'Ksl123456KENAM';

echo "Configuration utilisée :\n";
echo "  Host: $host:$port\n";
echo "  Database: $database\n";
echo "  Username: $username\n";
echo "  Password: " . str_repeat('*', strlen($password)) . "\n\n";

try {
    // Test de connexion PDO
    echo "1. Test de connexion PDO :\n";
    $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ];
    
    $pdo = new PDO($dsn, $username, $password, $options);
    echo "   Status: CONNEXION RÉUSSIE\n\n";
    
    // Informations sur le serveur
    echo "2. Informations serveur MySQL :\n";
    $stmt = $pdo->query("SELECT VERSION() as version");
    $version = $stmt->fetch();
    echo "   Version MySQL: " . $version['version'] . "\n";
    
    $stmt = $pdo->query("SELECT DATABASE() as current_db");
    $currentDb = $stmt->fetch();
    echo "   Base actuelle: " . $currentDb['current_db'] . "\n";
    
    $stmt = $pdo->query("SELECT USER() as current_user");
    $currentUser = $stmt->fetch();
    echo "   Utilisateur: " . $currentUser['current_user'] . "\n\n";
    
    // Vérification des tables importantes
    echo "3. Vérification des tables critiques :\n";
    $tablesCritiques = [
        'users',
        'migrations',
        'juridique_contrats',
        'juridique_documents',
        'juridique_financements',
        'operations',
        'vehicules',
        'pointages'
    ];
    
    foreach ($tablesCritiques as $table) {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = ? AND table_name = ?");
        $stmt->execute([$database, $table]);
        $result = $stmt->fetch();
        
        if ($result['count'] > 0) {
            echo "   $table: EXISTS\n";
            
            // Compter les enregistrements
            try {
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
                $count = $stmt->fetch();
                echo "     Enregistrements: " . $count['count'] . "\n";
            } catch (Exception $e) {
                echo "     Erreur comptage: " . $e->getMessage() . "\n";
            }
        } else {
            echo "   $table: MISSING\n";
        }
    }
    
    echo "\n4. Test des permissions :\n";
    
    // Test SELECT
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch();
        echo "   SELECT (users): " . $result['count'] . " utilisateurs\n";
    } catch (Exception $e) {
        echo "   SELECT (users): ERREUR - " . $e->getMessage() . "\n";
    }
    
    // Test INSERT
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS test_connection (
            id INT AUTO_INCREMENT PRIMARY KEY,
            test_value VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        $stmt = $pdo->prepare("INSERT INTO test_connection (test_value) VALUES (?)");
        $stmt->execute(['test_' . date('YmdHis')]);
        echo "   INSERT: OK\n";
        
        // Nettoyer
        $pdo->exec("DELETE FROM test_connection WHERE test_value = 'test_" . date('YmdHis') . "'");
    } catch (Exception $e) {
        echo "   INSERT: ERREUR - " . $e->getMessage() . "\n";
    }
    
    // Test CREATE TABLE
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS test_permissions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        echo "   CREATE TABLE: OK\n";
    } catch (Exception $e) {
        echo "   CREATE TABLE: ERREUR - " . $e->getMessage() . "\n";
    }
    
    // Test ALTER TABLE
    try {
        $pdo->exec("ALTER TABLE test_permissions ADD COLUMN test_field VARCHAR(100)");
        echo "   ALTER TABLE: OK\n";
        $pdo->exec("ALTER TABLE test_permissions DROP COLUMN test_field");
    } catch (Exception $e) {
        echo "   ALTER TABLE: ERREUR - " . $e->getMessage() . "\n";
    }
    
    echo "\n5. Vérification des migrations :\n";
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM migrations");
        $result = $stmt->fetch();
        echo "   Migrations enregistrées: " . $result['count'] . "\n";
        
        if ($result['count'] > 0) {
            $stmt = $pdo->query("SELECT migration FROM migrations ORDER BY id DESC LIMIT 5");
            $migrations = $stmt->fetchAll();
            echo "   Dernières migrations:\n";
            foreach ($migrations as $migration) {
                echo "     - " . $migration['migration'] . "\n";
            }
        }
    } catch (Exception $e) {
        echo "   Erreur lecture migrations: " . $e->getMessage() . "\n";
    }
    
    echo "\n6. Recommandations :\n";
    
    // Vérifier si les tables juridiques existent
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = ? AND table_name LIKE 'juridique_%'");
    $stmt->execute([$database]);
    $result = $stmt->fetch();
    
    if ($result['count'] == 0) {
        echo "   ATTENTION: Les tables juridiques n'existent pas.\n";
        echo "   Exécutez: php artisan migrate --force\n";
    } else {
        echo "   Tables juridiques: " . $result['count'] . " trouvées\n";
    }
    
    // Vérifier le cache
    echo "   Cache configuré: " . (getenv('CACHE_DRIVER') ?: 'non défini') . "\n";
    echo "   Session driver: " . (getenv('SESSION_DRIVER') ?: 'non défini') . "\n";
    
    echo "\n=== DIAGNOSTIC TERMINÉ AVEC SUCCÈS ===\n";
    
} catch (PDOException $e) {
    echo "   Status: ERREUR DE CONNEXION\n";
    echo "   Code: " . $e->getCode() . "\n";
    echo "   Message: " . $e->getMessage() . "\n\n";
    
    echo "SOLUTIONS POSSIBLES:\n";
    
    if (strpos($e->getMessage(), 'Access denied') !== false) {
        echo "1. Vérifier le nom d'utilisateur et le mot de passe\n";
        echo "2. Demander à l'hébergeur de créer/recréer l'utilisateur:\n";
        echo "   CREATE USER 'seke9323_ksl'@'localhost' IDENTIFIED BY 'Ksl123456KENAM';\n";
        echo "   GRANT ALL PRIVILEGES ON seke9323_kenam.* TO 'seke9323_ksl'@'localhost';\n";
        echo "   FLUSH PRIVILEGES;\n";
    }
    
    if (strpos($e->getMessage(), 'No such host') !== false || strpos($e->getMessage(), "Can't connect") !== false) {
        echo "1. Vérifier que le serveur MySQL fonctionne\n";
        echo "2. Vérifier l'adresse du serveur MySQL (localhost)\n";
        echo "3. Vérifier que le port 3306 est accessible\n";
        echo "4. Essayer avec l'IP du serveur MySQL au lieu de localhost\n";
    }
    
    if (strpos($e->getMessage(), 'Unknown database') !== false) {
        echo "1. Créer la base de données:\n";
        echo "   CREATE DATABASE seke9323_kenam CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\n";
        echo "2. Donner les permissions à l'utilisateur\n";
    }
    
    echo "\nCommandes de test utiles:\n";
    echo "# Test direct MySQL:\n";
    echo "mysql -u seke9323_ksl -p seke9323_kenam\n";
    echo "\n# Test avec PHP:\n";
    echo "php artisan tinker --execute=\"DB::connection()->getPdo(); echo 'DB OK';\"\n";
}
?>
