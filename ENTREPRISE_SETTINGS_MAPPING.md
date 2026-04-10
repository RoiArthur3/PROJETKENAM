# Mapping Complet - Paramètres Entreprise / Enterprise Settings

## 📋 Résumé Exécutif
Ce document documente toute la structure concernant la gestion des paramètres entreprise dans l'application KENAM Services.

---

## 🎯 Controllers

### 1. **ParametrageController**
**Chemin:** `app/Http/Controllers/ParametrageController.php`

| Méthode | Verbe HTTP | Route | Description |
|---------|-----------|-------|-------------|
| `index()` | GET | `/parametrage` | Accueil avec statistiques |
| `entreprise()` | GET | `/parametrage/entreprise` | Affiche le formulaire entreprise |
| `saveEntreprise()` | POST | `/parametrage/entreprise` | Sauvegarde les infos entreprise |
| `email()` | GET | `/parametrage/email` | Affiche le formulaire email |
| `saveEmail()` | POST | `/parametrage/email` | Sauvegarde la config email (retour JSON) |
| `testEmail()` | POST | `/parametrage/email/test` | Test d'envoi email (retour JSON) |
| `general()` | GET | `/parametrage/general` | Affiche les paramètres généraux |
| `saveGeneral()` | POST | `/parametrage/general` | Sauvegarde les paramètres généraux |
| `systeme()` | GET | `/parametrage/systeme` | Affiche les paramètres système |
| `saveSysteme()` | POST | `/parametrage/systeme` | Sauvegarde les paramètres système |
| `services()` | GET | `/parametrage/services` | Affiche la config des services |
| `saveServices()` | POST | `/parametrage/services` | Sauvegarde la config des services |
| `hikvisionConfig()` | GET | `/parametrage/hikvision/config` | Affiche config Hikvision |
| `saveHikvisionConfig()` | POST | `/parametrage/hikvision/config` | Sauvegarde config Hikvision |
| `testHikvision()` | POST | `/parametrage/hikvision/test` | Test connexion Hikvision (retour JSON) |
| `hikvisionDiagnostic()` | GET | `/parametrage/hikvision/diagnostic` | Affiche page diagnostic Hikvision |
| `runHikvisionDiagnostic()` | POST | `/parametrage/hikvision/diagnostic` | Lance diagnostic (retour JSON) |
| `saveSms()` | POST | `/parametrage/sms` | Sauvegarde config SMS (retour JSON) ⚠️ *Non en ligne* |

### 2. **SettingsController**
**Chemin:** `app/Http/Controllers/SettingsController.php`

| Méthode | Verbe HTTP | Route | Description |
|---------|-----------|-------|-------------|
| `companySettings()` | GET | `/settings/company` | Affiche le formulaire entreprise (alternative) |
| `updateCompanySettings()` | POST | `/settings/company` | Sauvegarde les infos entreprise |
| `usersIndex()` | GET | `/settings/users` | Gestion utilisateurs |
| `createUser()` | GET | `/settings/users/create` | Création utilisateur |
| `storeUser()` | POST | `/settings/users` | Stocke nouvel utilisateur |
| `editUser()` | GET | `/settings/users/{user}/edit` | Édition utilisateur |
| `updateUser()` | PUT | `/settings/users/{user}` | Mise à jour utilisateur |
| `destroyUser()` | DELETE | `/settings/users/{user}` | Suppression utilisateur |
| `rolesIndex()` | GET | `/settings/roles` | Gestion rôles |
| `generalSettings()` | GET | `/settings/general` | Paramètres généraux |
| `updateGeneralSettings()` | POST | `/settings/general` | Mise à jour paramètres généraux |
| `notificationSettings()` | GET | `/settings/notifications` | Paramètres notifications |
| `updateNotificationSettings()` | POST | `/settings/notifications` | Mise à jour notifications |

---

## 🛣️ Routes Définies

### Via `routes/web.php`
```php
// Module Paramétrage (lignes ~160-190)
Route::prefix('parametrage')->name('parametrage.')->group(function () {
    Route::get('/', [ParametrageController::class, 'index'])->name('index');
    Route::get('/entreprise', [ParametrageController::class, 'entreprise'])->name('entreprise');
    Route::post('/entreprise', [ParametrageController::class, 'saveEntreprise'])->name('entreprise.save');
    
    Route::get('/email', [ParametrageController::class, 'email'])->name('email');
    Route::post('/email', [ParametrageController::class, 'saveEmail'])->name('email.save');
    Route::post('/email/test', [ParametrageController::class, 'testEmail'])->name('email.test');
    
    Route::get('/general', [ParametrageController::class, 'general'])->name('general');
    Route::post('/general', [ParametrageController::class, 'saveGeneral'])->name('general.save');
    
    Route::get('/systeme', [ParametrageController::class, 'systeme'])->name('systeme');
    Route::post('/systeme', [ParametrageController::class, 'saveSysteme'])->name('systeme.save');
    
    Route::post('/sms', [ParametrageController::class, 'saveSms'])->name('sms.save');
    
    Route::get('/services', [ParametrageController::class, 'services'])->name('services');
    Route::post('/services', [ParametrageController::class, 'saveServices'])->name('services.save');
    
    // Hikvision routes
    Route::get('/hikvision/config', [ParametrageController::class, 'hikvisionConfig'])->name('hikvision.config');
    Route::post('/hikvision/config', [ParametrageController::class, 'saveHikvisionConfig'])->name('hikvision.config.save');
    Route::post('/hikvision/test', [ParametrageController::class, 'testHikvision'])->name('hikvision.test');
    Route::get('/hikvision/diagnostic', [ParametrageController::class, 'hikvisionDiagnostic'])->name('hikvision.diagnostic');
    Route::post('/hikvision/diagnostic', [ParametrageController::class, 'runHikvisionDiagnostic'])->name('hikvision.diagnostic.run');
});

// Module Settings (alias compatible sidebar)
Route::prefix('settings')->name('settings.')->group(function () {
    Route::get('/', [ParametrageController::class, 'index'])->name('index');
    Route::get('/company', [ParametrageController::class, 'entreprise'])->name('company.index');
    Route::post('/company', [ParametrageController::class, 'saveEntreprise'])->name('company.update');
});
```

### Via `routes/web-settings.php`
```php
Route::prefix('settings')->name('settings.')->middleware(['auth'])->group(function () {
    Route::get('/', [SettingsController::class, 'index'])->name('index');
    
    // Gestion utilisateurs
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [SettingsController::class, 'usersIndex'])->name('index');
        Route::get('/create', [SettingsController::class, 'createUser'])->name('create');
        Route::post('/', [SettingsController::class, 'storeUser'])->name('store');
        Route::get('/{user}/edit', [SettingsController::class, 'editUser'])->name('edit');
        Route::put('/{user}', [SettingsController::class, 'updateUser'])->name('update');
        Route::delete('/{user}', [SettingsController::class, 'destroyUser'])->name('destroy');
    });
    
    // Rôles et permissions
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [SettingsController::class, 'rolesIndex'])->name('index');
        Route::get('/create', [SettingsController::class, 'createRole'])->name('create');
        Route::post('/', [SettingsController::class, 'storeRole'])->name('store');
        Route::get('/{role}/edit', [SettingsController::class, 'editRole'])->name('edit');
        Route::put('/{role}', [SettingsController::class, 'updateRole'])->name('update');
        Route::delete('/{role}', [SettingsController::class, 'destroyRole'])->name('destroy');
    });

    // Paramètres de l'entreprise
    Route::prefix('company')->name('company.')->group(function () {
        Route::get('/', [SettingsController::class, 'companySettings'])->name('index');
        Route::post('/', [SettingsController::class, 'updateCompanySettings'])->name('update');
    });
    
    // Autres modules...
});
```

---

## 📊 Modèle de Données

### **EntrepriseSettings**
**Chemin:** `app/Models/EntrepriseSettings.php`

#### Champs fillable
```php
protected $fillable = [
    'nom_entreprise',
    'sigle',
    'adresse',
    'telephone',
    'email_contact',
    'site_web',
    'rccm',
    'compte_bancaire',
    'logo_path',
    'ifu',
    'cnss',
    'tva_rate',
    'email_tresorerie',
    'email_caisse_1',
    'email_caisse_2',
    'email_destinataire_principal',
    'email_dg',
    'seuil_validation_dg',
    'seuil_validation_principal',
    'seuil_validation_dg_force',
    'email_noreply',
    'email_support',
    'telephone_support',
    // Configuration SMS NetSMSPro
    'sms_provider',
    'sms_api_key',
    'sms_api_secret',
    'sms_username',
    'sms_password',
    'sms_sender_id',
    'sms_reseller_code',
    'sms_api_url',
    'sms_is_active',
    'actif',
];
```

#### Méthodes statiques
- `getActive()` - Récupère les paramètres actifs
- `getValue($key, $default = null)` - Récupère une valeur spécifique

---

## 🎨 Vues (Blade Templates)

### Via `/parametrage`
- **[parametrage/index.blade.php](resources/views/parametrage/index.blade.php)** - Tableau de bord avec cartes
- **[parametrage/entreprise.blade.php](resources/views/parametrage/entreprise.blade.php)** - Formulaire complet (onglets)
  - Onglet: Informations Entreprise
  - Onglet: Configuration Email
  - Onglet: Configuration SMS
  - Onglet: Paramètres Système
- **[parametrage/email.blade.php](resources/views/parametrage/email.blade.php)** - Config email (standalone)
- **[parametrage/systeme.blade.php](resources/views/parametrage/systeme.blade.php)** - Config système
- **[parametrage/services.blade.php](resources/views/parametrage/services.blade.php)** - Config des services
- **[parametrage/facial-recognition.blade.php](resources/views/parametrage/facial-recognition.blade.php)** - Config reconnaissance faciale
- **[parametrage/hikvision.blade.php](resources/views/parametrage/hikvision.blade.php)** - Config Hikvision

### Via `/settings`
- **[settings/index.blade.php](resources/views/settings/index.blade.php)** - Tableau de bord settings
- **[settings/company/index.blade.php](resources/views/settings/company/index.blade.php)** - Formulaire entreprise (alternative)
- **[settings/users/index.blade.php](resources/views/settings/users/index.blade.php)** - Liste utilisateurs
- **[settings/users/create.blade.php](resources/views/settings/users/create.blade.php)** - Création utilisateur
- **[settings/users/edit.blade.php](resources/views/settings/users/edit.blade.php)** - Édition utilisateur

---

## 📋 Formulaires & Champs

### ✅ Informations Entreprise (Formulaire Principal)
```
Informations Générales:
├── nom_entreprise (text, required)
├── sigle (text, max:10, required)
├── adresse (textarea, required)
├── telephone (tel, max:20, required)
├── email_contact (email, required)
├── site_web (url, required)
├── ifu (text, optional)
├── cnss (text, optional)
├── rccm (text, optional)
└── tva_rate (number, optional)

Identifiants Fiscaux:
├── rc (text, max:255, optional)
└── cc (text, max:255, optional)

Configuration Financière:
├── email_tresorerie (email, optional)
├── email_caisse_1 (email, optional)
├── email_caisse_2 (email, optional)
├── email_destinataire_principal (email, optional)
├── email_dg (email, optional)
├── seuil_validation_dg (number, optional)
├── seuil_validation_principal (number, optional)
└── seuil_validation_dg_force (number, optional)

Support & Notifications:
├── email_noreply (email, optional)
├── email_support (email, optional)
└── telephone_support (tel, optional)

Logo:
└── logo (file, image, max:2048, optional)
```

### 📧 Configuration Email
```
Paramètres SMTP:
├── mail_mailer (select: smtp|mail|sendmail)
├── mail_host (text, required)
├── mail_port (number, required)
├── mail_encryption (select: tls|ssl|none)
├── mail_username (text, required)
├── mail_password (password, required)
├── mail_from_address (email, required)
└── mail_from_name (text, required)

Test Email:
├── test_email (email, required)
├── test_subject (text)
└── test_message (textarea)
```

### 📱 Configuration SMS NetSMSPro
```
Paramètres SMS:
├── sms_provider (select: netsmspro|smseco|smseco_revendeur|etc.)
├── sms_sender_id (text, max:20)
├── sms_is_active (checkbox)
├── sms_api_key (text)
├── sms_api_secret (password)
├── sms_username (text, optional)
├── sms_password (password, optional)
├── sms_reseller_code (text, optional)
└── sms_api_url (url, optional)
```

### ⚙️ Paramètres Système
```
Configuration:
├── app_timezone (select)
├── app_locale (select: fr|en)
├── session_lifetime (number, min:15, max:480)
├── max_file_size (number, min:1, max:100)
├── debug_mode (checkbox)
├── maintenance_mode (checkbox)
└── backup_frequency (select: daily|weekly|monthly)
```

---

## 🔌 Mécanisme AJAX

### Soumission de Formulaires
**Fichier:** `resources/views/parametrage/entreprise.blade.php` (lignes 624-627)

```javascript
// Initialisation des gestionnaires de soumission
handleFormSubmit('entrepriseForm', '{{ route("parametrage.entreprise.save") }}');
handleFormSubmit('emailForm', '{{ route("parametrage.email.save") }}');
handleFormSubmit('smsForm', '{{ route("parametrage.sms.save") }}');
handleFormSubmit('systemeForm', '{{ route("parametrage.systeme.save") }}');
```

### Fonction Générique
```javascript
function handleFormSubmit(formId, url) {
    const form = document.getElementById(formId);
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Traiter la réponse JSON
            if (data.success) {
                // Afficher succès
            } else {
                // Afficher erreurs
            }
        });
    });
}
```

### Format des Réponses JSON

#### ✅ Succès
```json
{
    "success": true,
    "message": "Informations de l'entreprise sauvegardées avec succès"
}
```

#### ❌ Erreur Validation
```json
{
    "success": false,
    "errors": {
        "nom": ["Le champ nom est requis"],
        "email_contact": ["L'email est invalide"]
    }
}
```

#### ❌ Erreur Serveur
```json
{
    "success": false,
    "message": "Erreur lors de la sauvegarde: [détail erreur]"
}
```

---

## 🔐 Middleware & Authentification

Toutes les routes sont protégées par:
- `auth` middleware - Nécessite d'être connecté
- Permissions implicites - Rôles admin/superadmin requis

```php
Route::middleware(['auth'])->group(function () {
    // Toutes les routes de paramétrage
});
```

---

## 🧪 Routes de Test JSON (Hikvision)

### Test Hikvision
**Route:** `POST /parametrage/hikvision/test`
**Réponse:**
```json
{
    "success": true,
    "message": "Connexion établie avec succès",
    "device_info": {
        "model": "DS-2CD2043G0-I",
        "firmware": "V5.5.0",
        "serial": "DS-2CD2043G0-I20230401AACH123456789"
    }
}
```

### Diagnostic Hikvision
**Route:** `POST /parametrage/hikvision/diagnostic`
**Réponse:**
```json
{
    "success": true,
    "results": {
        "connectivity": {
            "status": "success",
            "message": "Connectivité OK",
            "response_time": "45ms"
        },
        "authentication": {
            "status": "success",
            "message": "Authentification réussie"
        },
        "devices": {
            "status": "success",
            "count": 3,
            "devices": [...]
        },
        "services": {
            "status": "warning",
            "message": "Service de reconnaissance faciale nécessite redémarrage"
        }
    }
}
```

---

## ⚠️ Points Important

1. **Méthode saveSms() absente** - Existe dans le backup mais pas en ligne (`ParametrageController.php.backup`)
2. **Route POST /sms existe** mais la méthode correspondante n'est pas implémentée dans la version actuelle
3. **Dual System** - Deux systèmes de routes coexistent:
   - `/parametrage/*` (ParametrageController)
   - `/settings/*` (SettingsController)
4. **AJAX Handling** - Les formulaires utilisent AJAX pour une meilleure UX
5. **File Upload** - Support de l'upload de logo (image, max 2MB)
6. **Fichier .env** - La méthode `updateEnvFile()` modifie directement `.env`

---

## 📦 Dépendances Modèles

- **EntrepriseSettings** - Table centralisée pour tous les paramètres
- **ServiceOperationnel** - Lié pour la configuration des services
- **User** - Lié pour les permissions utilisateurs
- **Role** - Lié pour les rôles

---

## 🔗 Fichiers Liés

**Configuration:**
- `config/app.php` - Paramètres application
- `config/hikvision.php` - Configuration Hikvision
- `config/sms.php` - Configuration SMS
- `.env` - Fichier d'environnement

**Migrations:**
- Migration pour `enterprise_settings` table

**Tests:**
- `check_operations_structure.php` - Diagnostic
- `test_hikvision.php` - Test Hikvision
- `deploy-facial-config.*` - Déploiement facial

---

## 📈 Statistiques (Dashboard)

Le dashboard `/parametrage` affiche:
- Nombre total d'utilisateurs
- Utilisateurs actifs
- Services opérationnels
- Types d'opérations
- Opérations totales
- Opérations en attente
- Opérations approuvées
- Opérations rejetées

---

**Document généré:** 27 Mars 2026  
**Dernière mise à jour:** 2026-03-27  
**Version Laravel:** 10.x  
**Base de données:** MySQL/MariaDB
