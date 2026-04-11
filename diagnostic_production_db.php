<?php
echo "=== DIAGNOSTIC BASE DE DONNÉES PRODUCTION ===\n\n";

// Test de connexion à la base de données
try {
    $host = '127.0.0.1';
    $port = 3306;
    $database = 'seke9323_kenam';
    $username = 'seke9323_ksl';
    $password = 'Kenam123456*';
    
    echo "1. Test de connexion MySQL:\n";
    echo "   Host: $host:$port\n";
    echo "   Database: $database\n";
    echo "   Username: $username\n";
    echo "   Password: " . str_repeat('*', strlen($password)) . "\n\n";
    
    // Test avec PDO
    $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    
    $pdo = new PDO($dsn, $username, $password, $options);
    echo "   Status: CONNEXION RÉUSSIE\n\n";
    
    // Vérifier les tables
    echo "2. Vérification des tables:\n";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll();
    
    foreach ($tables as $table) {
        $tableName = array_values($table)[0];
        echo "   - $tableName\n";
    }
    
    echo "\n3. Vérification des permissions:\n";
    
    // Test SELECT
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch();
        echo "   SELECT (users): " . $result['count'] . " enregistrements\n";
    } catch (Exception $e) {
        echo "   SELECT (users): ERREUR - " . $e->getMessage() . "\n";
    }
    
    // Test INSERT
    try {
        $stmt = $pdo->prepare("INSERT INTO test_table (name) VALUES (?)");
        $stmt->execute(['test_' . date('YmdHis')]);
        echo "   INSERT: OK\n";
        $pdo->exec("DELETE FROM test_table WHERE name = 'test_" . date('YmdHis') . "'");
    } catch (Exception $e) {
        echo "   INSERT: ERREUR - " . $e->getMessage() . "\n";
    }
    
    // Test CREATE TABLE
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS test_table (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        echo "   CREATE TABLE: OK\n";
    } catch (Exception $e) {
        echo "   CREATE TABLE: ERREUR - " . $e->getMessage() . "\n";
    }
    
    echo "\n4. Informations sur le serveur:\n";
    $stmt = $pdo->query("SELECT VERSION() as version");
    $version = $stmt->fetch();
    echo "   MySQL Version: " . $version['version'] . "\n";
    
    $stmt = $pdo->query("SELECT DATABASE() as current_db");
    $currentDb = $stmt->fetch();
    echo "   Database actuelle: " . $currentDb['current_db'] . "\n";
    
    $stmt = $pdo->query("SELECT USER() as current_user");
    $currentUser = $stmt->fetch();
    echo "   Utilisateur actuel: " . $currentUser['current_user'] . "\n";
    
    echo "\n=== DIAGNOSTIC TERMINÉ ===\n";
    
} catch (PDOException $e) {
    echo "   Status: ERREUR DE CONNEXION\n";
    echo "   Message: " . $e->getMessage() . "\n";
    echo "   Code: " . $e->getCode() . "\n\n";
    
    // Suggestions basées sur l'erreur
    if (strpos($e->getMessage(), 'Access denied') !== false) {
        echo "SUGGESTIONS:\n";
        echo "1. Vérifier le nom d'utilisateur et le mot de passe\n";
        echo "2. Vérifier que l'utilisateur a les permissions sur la base de données\n";
        echo "3. Vérifier que la base de données existe\n";
        echo "4. Vérifier que le serveur MySQL est accessible\n";
    }
    
    if (strpos($e->getMessage(), 'No such host') !== false) {
        echo "SUGGESTIONS:\n";
        echo "1. Vérifier l'adresse du serveur MySQL\n";
        echo "2. Vérifier que le port 3306 est ouvert\n";
        echo "3. Vérifier que le service MySQL fonctionne\n";
    }
}
?>
