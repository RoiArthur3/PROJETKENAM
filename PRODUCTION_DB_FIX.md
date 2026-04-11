# Guide de Résolution - Erreur Connexion Base de Données Production

## Problème
```
SQLSTATE[HY000] [1045] Access denied for user 'seke9323_ksl'@'localhost' (using password: YES)
```

## Solutions Rapides

### 1. Corriger le fichier .env.production

**Connectez-vous au serveur et modifiez :**
```bash
# Sur le serveur de production
cd /var/www/html/ksl.kenamservices.net
nano .env
```

**Vérifiez/corrigez ces lignes :**
```env
DB_CONNECTION=mysql
DB_HOST=localhost          # ou l'IP du serveur MySQL
DB_PORT=3306
DB_DATABASE=seke9323_kenam
DB_USERNAME=seke9323_ksl    # CORRIGÉ
DB_PASSWORD=Kenam123456*
```

### 2. Vérifier l'accès MySQL

**Test direct depuis le serveur :**
```bash
mysql -u seke9323_ksl -p seke9323_kenam
# Entrez le mot de passe : Kenam123456*
```

**Si ça ne fonctionne pas, essayez :**
```bash
# Vérifier si l'utilisateur existe
mysql -u root -p
SHOW DATABASES;
SHOW GRANTS FOR 'seke9323_ksl'@'localhost';
```

### 3. Créer l'utilisateur si nécessaire

**Depuis MySQL en tant que root :**
```sql
-- Créer l'utilisateur
CREATE USER 'seke9323_ksl'@'localhost' IDENTIFIED BY 'Kenam123456*';

-- Donner les permissions
GRANT ALL PRIVILEGES ON seke9323_kenam.* TO 'seke9323_ksl'@'localhost';

-- Appliquer les changements
FLUSH PRIVILEGES;

-- Vérifier
SHOW GRANTS FOR 'seke9323_ksl'@'localhost';
```

### 4. Vérifier la configuration de l'hôte

**Si MySQL n'est pas sur localhost :**
```bash
# Vérifier l'adresse du serveur MySQL
ping mysql.votredomaine.com
# ou
telnet mysql.votredomaine.com 3306
```

**Modifier .env si nécessaire :**
```env
DB_HOST=mysql.votredomaine.com
```

### 5. Diagnostic complet

**Exécutez le script de diagnostic :**
```bash
php diagnostic_production_db.php
```

## Étapes de Déploiement Corrigées

### 1. Backup avant modification
```bash
# Backup .env actuel
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)

# Backup base de données
mysqldump -u root -p seke9323_kenam > backup_db_$(date +%Y%m%d_%H%M%S).sql
```

### 2. Appliquer les corrections
```bash
# Mettre à jour .env avec les bonnes valeurs
nano .env

# Vider le cache
php artisan cache:clear
php artisan config:clear

# Tester la connexion
php artisan tinker --execute="DB::connection()->getPdo(); echo 'DB OK';"
```

### 3. Exécuter les migrations
```bash
php artisan migrate --force
```

## Problèmes Courants et Solutions

### Erreur: "No such host"
**Solution :** Vérifier `DB_HOST` dans `.env`

### Erreur: "Unknown database"
**Solution :** Créer la base de données
```sql
CREATE DATABASE seke9323_kenam CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Erreur: "Access denied"
**Solution :** Recréer l'utilisateur avec les bonnes permissions

### Erreur: "Can't connect to MySQL server"
**Solution :** Vérifier que MySQL fonctionne et que le port est ouvert

## Contact Support

Si le problème persiste :
1. Vérifier les logs : `storage/logs/laravel.log`
2. Contacter l'hébergeur pour vérifier la configuration MySQL
3. Demander les informations exactes de connexion à la base de données

## Vérification Finale

Après correction, testez :
```bash
php artisan migrate:status
php artisan tinker --execute="echo 'Application OK';"
```

---
**Note :** Conservez ce guide pour référence future et partagez-le avec l'équipe technique.
