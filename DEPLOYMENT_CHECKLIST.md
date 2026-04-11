# Checklist de Déploiement - Module Juridique et Mises à Jour

## Fichiers à zipper pour la mise en production

### 1. Dossiers principaux (obligatoires)
```
app/
  - Http/Controllers/Juridique/
  - Http/Controllers/ProjetsDashboardController.php
  - Http/Controllers/TresorerieController.php
  - Models/JuridiqueFinancement.php
  - Models/Operation.php
  - Models/OperationVehicule.php
  - Services/ProjectFinanceService.php
  - Services/TresorerieIntegrationService.php
  - Http/Requests/ProjectRequest.php

database/
  - migrations/2026_04_10_*.php
  - migrations/2026_04_11_*.php
  - seeders/VehiculeTestSeeder.php

resources/views/
  - juridique/
  - projets/
  - fournisseurs/
  - materiel/
  - tresorerie/

routes/
  - web-juridique.php
  - web-projets.php
  - web-tresorerie.php
```

### 2. Fichiers de configuration
```
config/
  - (si modifications récentes)

.env.production
  - (si modifications de configuration)
```

### 3. Assets publics
```
public/
  - storage/logos/favicon.png
  - (autres assets modifiés)
```

### 4. Commandes à exécuter en production

#### Étape 1: Backup
```bash
# Backup de la base de données
mysqldump -u username -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql

# Backup des fichiers actuels
cp -r app/ app_backup_$(date +%Y%m%d_%H%M%S)/
cp -r resources/views/ views_backup_$(date +%Y%m%d_%H%M%S)/
```

#### Étape 2: Déploiement
```bash
# 1. Mettre le site en maintenance
php artisan down

# 2. Extraire les fichiers zip
unzip kenam_update_$(date +%Y%m%d).zip

# 3. Exécuter les migrations
php artisan migrate --force

# 4. Vider le cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 5. Optimiser
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Remettre le site en ligne
php artisan up
```

### 5. Vérifications post-déploiement

#### Tests manuels
- [ ] Accès à `/juridique/documents` - bouton "Nouveau document"
- [ ] Accès à `/juridique/financements` - bouton "Nouveau dossier"
- [ ] Création d'un document juridique
- [ ] Création d'un dossier de financement
- [ ] Accès au dashboard projets
- [ ] Accès à la trésorerie
- [ ] Pointage d'engins fonctionnel

#### Vérifications techniques
- [ ] Tables juridiques créées
- [ ] Routes accessibles
- [ ] Permissions fonctionnelles
- [ ] Logs d'erreurs vides

### 6. Fichiers spécifiques à déployer

#### Nouveaux modèles
```
app/Models/JuridiqueFinancement.php
app/Models/OperationVehicule.php
```

#### Contrôleurs modifiés
```
app/Http/Controllers/Juridique/DocumentController.php
app/Http/Controllers/Juridique/FinancementController.php
app/Http/Controllers/ProjetsDashboardController.php
app/Http/Controllers/TresorerieController.php
```

#### Vues améliorées
```
resources/views/juridique/documents/index.blade.php
resources/views/juridique/documents/create.blade.php
resources/views/juridique/financements/index.blade.php
resources/views/juridique/financements/create.blade.php
```

#### Migrations critiques
```
database/migrations/2026_04_11_050000_create_juridique_tables.php
database/migrations/2026_04_10_*.php
```

### 7. Commande de création du zip

```bash
# Créer le zip de déploiement
zip -r kenam_update_$(date +%Y%m%d).zip \
  app/Http/Controllers/Juridique/ \
  app/Models/JuridiqueFinancement.php \
  app/Models/OperationVehicule.php \
  app/Services/ProjectFinanceService.php \
  app/Services/TresorerieIntegrationService.php \
  app/Http/Requests/ProjectRequest.php \
  database/migrations/2026_04_10_*.php \
  database/migrations/2026_04_11_*.php \
  database/seeders/VehiculeTestSeeder.php \
  resources/views/juridique/ \
  resources/views/projets/ \
  resources/views/fournisseurs/ \
  resources/views/materiel/ \
  resources/views/tresorerie/ \
  routes/web-juridique.php \
  routes/web-projets.php \
  routes/web-tresorerie.php \
  public/storage/logos/favicon.png
```

### 8. Notes importantes

#### Sécurité
- Toujours faire un backup avant déploiement
- Tester sur environnement de staging si possible
- Vérifier les permissions après déploiement

#### Performance
- Vider le cache après déploiement
- Optimiser les routes et vues
- Surveiller les logs d'erreurs

#### Compatibilité
- Vérifier la version PHP en production
- Vérifier les extensions requises
- Tester avec les données réelles du client

### 9. Rollback (si nécessaire)

```bash
# Restaurer les fichiers
cp -r app_backup_$(date +%Y%m%d_%H%M%S)/* app/
cp -r views_backup_$(date +%Y%m%d_%H%M%S)/* resources/views/

# Rollback de la base (si nécessaire)
mysql -u username -p database_name < backup_$(date +%Y%m%d_%H%M%S).sql

# Vider le cache et redémarrer
php artisan cache:clear
php artisan up
```

### 10. Contact support

En cas de problème :
1. Vérifier les logs : `storage/logs/laravel.log`
2. Vérifier les erreurs PHP : `/var/log/apache2/error.log`
3. Contacter le développeur avec les logs d'erreurs

---

**Date de création :** 11/04/2026  
**Version :** 1.0  
**Statut :** Prêt pour déploiement
