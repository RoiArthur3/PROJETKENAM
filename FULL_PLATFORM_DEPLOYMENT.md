# Déploiement Complet de la Plateforme Kenam

## Fichiers à zipper pour la mise en production complète

### 1. Structure complète du projet

#### Dossiers principaux à inclure
```
app/
  - Http/Controllers/ (tous les contrôleurs modifiés)
  - Models/ (tous les modèles nouveaux/modifiés)
  - Services/ (services financiers et intégration)
  - Http/Requests/ (requêtes de validation)
  - Http/Middleware/ (middleware modifiés)

database/
  - migrations/ (toutes les migrations 2026_04_*)
  - seeders/ (seeders de test)

resources/views/
  - juridique/ (module juridique complet)
  - projets/ (dashboard et vues projets)
  - fournisseurs/ (gestion fournisseurs et engins)
  - materiel/ (véhicules et pointage)
  - tresorerie/ (module trésorerie)
  - layouts/ (sidebar et layouts modifiés)

routes/
  - web-juridique.php
  - web-projects.php
  - tresorerie.php
  - web.php (si modifié)

public/
  - storage/ (assets modifiés)
  - (autres fichiers publics modifiés)

config/
  - (fichiers de configuration modifiés)
```

#### Fichiers spécifiques critiques
```
# Contrôleurs modifiés
app/Http/Controllers/Juridique/DocumentController.php
app/Http/Controllers/Juridique/FinancementController.php
app/Http/Controllers/ProjetsDashboardController.php
app/Http/Controllers/TresorerieController.php
app/Http/Controllers/DashboardController.php
app/Http/Controllers/OperationController.php

# Modèles nouveaux
app/Models/JuridiqueFinancement.php
app/Models/OperationVehicule.php
app/Models/JuridiqueContrat.php
app/Models/JuridiqueDocument.php
app/Models/JuridiqueOffreBancaire.php
app/Models/JuridiqueEcheancier.php

# Services
app/Services/ProjectFinanceService.php
app/Services/TresorerieIntegrationService.php
app/Services/ProjetFinancialService.php

# Migrations critiques
database/migrations/2026_04_10_094932_add_photo_profil_to_users_table.php
database/migrations/2026_04_10_130000_add_engin_id_to_commande_fournisseurs_table.php
database/migrations/2026_04_10_173000_add_projet_fields_to_operations_table.php
database/migrations/2026_04_10_174000_create_operation_vehicule_table.php
database/migrations/2026_04_10_212713_add_pricing_columns_to_vehicules_table.php
database/migrations/2026_04_10_182347_add_vehicle_pointage_columns_to_pointages_table.php
database/migrations/2026_04_11_042300_create_journal_comptables_table.php
database/migrations/2026_04_11_042400_create_comptabilite_tables.php
database/migrations/2026_04_11_042500_update_comptes_comptables_type_column.php
database/migrations/2026_04_11_050000_create_juridique_tables.php

# Vues principales
resources/views/juridique/documents/index.blade.php
resources/views/juridique/documents/create.blade.php
resources/views/juridique/financements/index.blade.php
resources/views/juridique/financements/create.blade.php
resources/views/projets/dashboard.blade.php
resources/views/fournisseurs/create-engins.blade.php
resources/views/materiel/cost-control/engin-pointage-modal.blade.php
resources/views/layouts/sidebar-superadmin.blade.php
```

### 2. Commande de création du zip complet

```bash
# Créer le zip complet de la plateforme
powershell "Compress-Archive -Path 'app/','database/','resources/','routes/','public/','config/','composer.json','composer.lock','artisan','.env.example','package.json','vite.config.js','tailwind.config.js' -DestinationPath 'kenam_platform_full_$(Get-Date -Format yyyyMMdd).zip' -Force"
```

### 3. Étapes de déploiement complet

#### Phase 1: Préparation
```bash
# 1. Backup complet
mysqldump -u username -p database_name > full_backup_$(date +%Y%m%d_%H%M%S).sql
tar -czf full_files_backup_$(date +%Y%m%d_%H%M%S).tar.gz app/ database/ resources/ routes/ public/ config/

# 2. Maintenance
php artisan down
```

#### Phase 2: Déploiement
```bash
# 3. Extraire le zip complet
unzip kenam_platform_full_20260411.zip

# 4. Installer les dépendances (si nécessaire)
composer install --no-dev --optimize-autoloader
npm install --production
npm run build

# 5. Permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/

# 6. Migrations
php artisan migrate --force

# 7. Cache et optimisation
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Remise en ligne
php artisan up
```

### 4. Vérifications post-déploiement

#### Tests fonctionnels complets
- [ ] Page d'accès `/` fonctionnelle
- [ ] Login/logout fonctionnels
- [ ] Dashboard accessible
- [ ] Module juridique complet
  - [ ] Documents juridiques
  - [ ] Financements
  - [ ] Contrats
- [ ] Module projets
  - [ ] Dashboard projets
  - [ ] Création/édition projets
- [ ] Module trésorerie
  - [ ] Dashboard trésorerie
  - [ ] Opérations financières
- [ ] Module fournisseurs
  - [ ] Gestion fournisseurs
  - [ ] Pointage d'engins
- [ ] Module matériel
  - [ ] Véhicules
  - [ ] Pointage et suivi
- [ ] Module comptabilité
  - [ ] Journal comptable
  - [ ] Bilans et rapports

#### Tests techniques
- [ ] Base de données à jour
- [ ] Routes accessibles
- [ ] Permissions fonctionnelles
- [ ] Logs d'erreurs vides
- [ ] Performance acceptable
- [ ] Emails fonctionnels

### 5. Nouvelles fonctionnalités déployées

#### Module Juridique
- Gestion des contrats juridiques
- Gestion des documents juridiques
- Dossiers de financement
- Offres bancaires et échéanciers

#### Module Projets
- Dashboard amélioré
- Calculs financiers automatiques
- Rapports financiers

#### Module Trésorerie
- Dashboard trésorerie
- Intégration comptabilité
- Gestion des flux financiers

#### Module Fournisseurs
- Gestion des fournisseurs
- Pointage d'engins avec modal
- Suivi des opérations

#### Module Matériel
- Gestion des véhicules
- Pointage et suivi
- Calculs de coûts

#### Module Comptabilité
- Journal comptable
- Comptes généraux
- Rapports financiers

### 6. Sécurité et performance

#### Mesures de sécurité
- [ ] Validation des entrées
- [ ] Protection CSRF
- [ ] Permissions vérifiées
- [ ] Logs activés

#### Optimisation
- [ ] Cache configuré
- [ ] Routes optimisées
- [ ] Vues compilées
- [ ] Base de données optimisée

### 7. Monitoring et support

#### Surveillance
- [ ] Logs d'erreurs surveillés
- [ ] Performance monitorée
- [ ] Utilisation ressources

#### Support
- [ ] Documentation accessible
- [ ] Contacts support à jour
- [ ] Procédures de rollback

### 8. Rollback complet (si nécessaire)

```bash
# Rollback base de données
mysql -u username -p database_name < full_backup_$(date +%Y%m%d_%H%M%S).sql

# Rollback fichiers
tar -xzf full_files_backup_$(date +%Y%m%d_%H%M%S).tar.gz

# Cache et redémarrage
php artisan cache:clear
php artisan up
```

---

**Important :** Ce déploiement inclut TOUTE la plateforme avec toutes les modifications récentes. Assurez-vous d'avoir des backups complets avant de procéder.

**Date :** 11/04/2026  
**Version :** Complète  
**Taille estimée :** ~50-100 MB (compressé)
