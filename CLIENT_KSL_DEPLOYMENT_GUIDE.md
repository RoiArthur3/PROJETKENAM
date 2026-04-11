# Guide de Déploiement - Client KSL

## Configuration Actuelle du Client

### Informations Base de Données
- **Hôte**: localhost
- **Base**: seke9323_kenam
- **Utilisateur**: seke9323_ksl
- **Mot de passe**: Ksl123456KENAM

### Configuration Optimisée
Un fichier `.env.production.client` a été créé avec votre configuration optimisée.

## Étapes de Déploiement

### 1. Préparation du Serveur

```bash
# Connectez-vous au serveur
ssh seke9323@folina.o2switch.net

# Naviguer vers le répertoire
cd /var/www/html/ksl.kenamservices.net

# Backup avant modification
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
```

### 2. Test de Connexion Base de Données

```bash
# Exécuter le diagnostic personnalisé
php diagnostic_client_db.php
```

**Si le diagnostic réussit :**
- Continuez avec l'étape 3

**Si le diagnostic échoue :**
- Contactez votre hébergeur avec les résultats du diagnostic
- Suivez les solutions proposées dans le diagnostic

### 3. Déploiement des Fichiers

```bash
# Option A: Si vous avez le zip
unzip kenam_production_ready_20260411.zip

# Option B: Copie manuelle des fichiers corrigés
# Remplacer .env par .env.production.client
```

### 4. Configuration Finale

```bash
# Remplacer le .env actuel
cp .env.production.client .env

# Vider les caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimiser
php artisan config:cache
php artisan route:cache
```

### 5. Migration de la Base de Données

```bash
# Exécuter les migrations
php artisan migrate --force

# Vérifier le statut
php artisan migrate:status
```

### 6. Vérification Finale

```bash
# Test de l'application
php artisan tinker --execute="echo 'Application OK';"

# Test des routes juridiques
curl -s https://ksl.kenamservices.net/juridique/documents | head -20
```

## Problèmes Courants et Solutions

### Erreur: "Access denied"
**Solution immédiate:**
```bash
# Demander à l'hébergeur d'exécuter:
CREATE USER IF NOT EXISTS 'seke9323_ksl'@'localhost' IDENTIFIED BY 'Ksl123456KENAM';
GRANT ALL PRIVILEGES ON seke9323_kenam.* TO 'seke9323_ksl'@'localhost';
FLUSH PRIVILEGES;
```

### Erreur: "Can't connect to MySQL server"
**Solution:**
1. Vérifier que MySQL fonctionne: `systemctl status mysql`
2. Vérifier le port: `netstat -tlnp | grep :3306`
3. Essayer avec 127.0.0.1 au lieu de localhost

### Erreur: "Unknown database"
**Solution:**
```bash
# Créer la base de données
mysql -u root -p
CREATE DATABASE seke9323_kenam CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## Configuration Optimisée Recommandée

### Sessions et Cache
```env
SESSION_DRIVER=file
CACHE_DRIVER=file
```
*Plus stable en production que database*

### Sécurité HTTPS
```env
SESSION_SECURE_COOKIE=true
SESSION_DOMAIN=.kenamservices.net
```
*Déjà configuré correctement*

### Logging
```env
LOG_LEVEL=error
LOG_CHANNEL=stack
```
*Réduit la taille des logs en production*

## Vérifications Post-Déploiement

### 1. Accès au Site
- URL: https://ksl.kenamservices.net
- Vérifier que le site s'affiche correctement

### 2. Module Juridique
- Accès: https://ksl.kenamservices.net/juridique/documents
- Test: Bouton "Nouveau document"
- Test: Bouton "Nouveau dossier"

### 3. Base de Données
```bash
# Vérifier les tables
php artisan tinker --execute="echo Schema::hasTable('juridique_contrats') ? 'Tables OK' : 'Tables Missing';"
```

### 4. Logs
```bash
# Vérifier les erreurs
tail -f storage/logs/laravel.log
```

## Support Technique

### Si problème persiste:
1. **Exécuter le diagnostic**: `php diagnostic_client_db.php`
2. **Vérifier les logs**: `storage/logs/laravel.log`
3. **Contacter l'hébergeur**: Avec les résultats du diagnostic

### Informations à fournir au support:
- Résultat du diagnostic_client_db.php
- Dernières erreurs dans les logs
- Configuration .env (sans mot de passe)

## Checklist Finale

- [ ] Backup effectué
- [ ] Diagnostic DB passé avec succès
- [ ] Fichiers déployés
- [ ] Configuration mise à jour
- [ ] Caches vidés
- [ ] Migrations exécutées
- [ ] Site accessible
- [ ] Module juridique fonctionnel
- [ ] Logs d'erreurs vides

---
**Note:** Gardez ce guide pour référence future. Il contient votre configuration spécifique et les étapes optimisées pour votre environnement.
