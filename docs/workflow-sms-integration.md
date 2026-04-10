# Intégration SMS dans les Workflows de Validation

## 🎯 **Objectif**

Coupler tous les emails de workflow avec des SMS automatiques pour une meilleure réactivité et une notification instantanée des validateurs et demandeurs.

## 🔄 **Architecture**

```
Workflow Validation → Email + SMS → Validateur/Demandeur
├── ValidationRequired → Email + SMS
├── ValidationCompleted → Email + SMS  
├── OperationApproved → Email + SMS
├── OperationRejected → Email + SMS
└── PaymentNotification → Email + SMS
```

## 📱 **Canaux de Notification**

### 1. **Email (existant)**
- Utilise Laravel Mail
- Templates Blade détaillés
- Pièces jointes et HTML

### 2. **SMS (nouveau)**
- Utilise SMSManager
- Templates prédéfinis
- Limite 160 caractères

## 🔧 **Implémentation Technique**

### **Notifications Modifiées**

#### `ValidationRequired`
```php
public function via($notifiable): array
{
    $channels = ['mail'];
    
    // Ajouter SMS si téléphone disponible
    if ($notifiable->telephone) {
        $channels[] = SMSChannel::class;
    }
    
    return $channels;
}

public function toSMS($notifiable): array
{
    return [
        'template' => 'validation_required',
        'data' => [
            'validateur' => $notifiable->name,
            'module' => ucfirst($this->validation->module_source),
            'titre' => $this->validation->titre,
            'url' => url('/validations/' . $this->validation->id)
        ]
    ];
}
```

#### `ValidationCompleted`
```php
public function toSMS($notifiable): array
{
    return [
        'template' => 'validation_completed',
        'data' => [
            'demandeur' => $notifiable->name,
            'titre' => $this->validation->titre,
            'statut' => $statusText,
            'commentaire' => $this->validation->commentaire ?? 'Aucun',
            'url' => url('/validations/' . $this->validation->id)
        ]
    ];
}
```

### **SMSChannel**
```php
class SMSChannel
{
    public function send($notifiable, Notification $notification)
    {
        if (!$notifiable->telephone) return;
        
        $message = $notification->toSMS($notifiable);
        $result = app(SMSManager::class)->sendTemplate(
            $notifiable->telephone,
            $message['template'],
            $message['data']
        );
        
        // Logging et statistiques
    }
}
```

## 📋 **Templates SMS**

### **Validation Required**
```
KENAM: {validateur}, validation requise pour {titre} ({module}/{type}). {description}. Voir: {url}
```

### **Validation Completed**
```
KENAM: {demandeur}, votre demande "{titre}" ({module}) est {statut}. {commentaire}. Détails: {url}
```

### **Operation Alert**
```
KENAM: Votre intervention est requise pour {reference} ({titre}) montant {montant} FCFA. Ouvrir: {url}
```

### **Payment Notification**
```
KENAM: Paiement effectué pour {reference} ({montant} FCFA). Mode: {mode_paiement}. Date: {date_paiement}
```

## 🔄 **Workflow Complet**

### 1. **Création Opération**
```
User → Crée opération → Étape validation → Email + SMS au valideur
```

### 2. **Validation/Rejet**
```
Validateur → Approuve/Rejette → Email + SMS au demandeur + Étape suivante
```

### 3. **Paiement**
```
Trésorerie → Marque payé → Email + SMS au demandeur + Clôture
```

## 📊 **Statistiques et Monitoring**

### **Email Statistics Service**
- Logs d'envoi
- Taux de succès
- Templates utilisés
- Erreurs

### **SMS Statistics Service**
- Logs d'envoi
- Provider utilisé
- Coût par SMS
- Échecs

### **Dashboard**
- Emails envoyés/jour
- SMS envoyés/jour
- Taux de livraison
- Erreurs par provider

## ⚙️ **Configuration**

### **Variables d'environnement**
```env
SMS_PROVIDER=netsmspro
NETSMPRO_USERNAME=votre_username
NETSMPRO_PASSWORD=votre_password
NETSMPRO_SENDER_ID=KENAM
```

### **SMS Templates**
Dans `config/sms.php`:
```php
'templates' => [
    'validation_required' => 'KENAM: {validateur}, validation requise...',
    'validation_completed' => 'KENAM: {demandeur}, votre demande...',
    // ... autres templates
],
```

## 🎯 **Cas d'Usage**

### **1. Validation Opération**
- **Email**: Détails complets + bouton d'action
- **SMS**: Notification rapide + lien direct

### **2. Approbation DG**
- **Email**: Rapport détaillé + pièces jointes
- **SMS**: Alerte urgente + référence

### **3. Paiement Effectué**
- **Email**: Reçu PDF + détails
- **SMS**: Confirmation immédiate

## 🚀 **Déploiement**

### 1. **Migration**
```bash
php artisan migrate
```

### 2. **Configuration**
- Vérifier variables SMS
- Configurer templates
- Tester providers

### 3. **Test**
```bash
php artisan tinker
$user = User::find(1);
$user->notify(new ValidationRequired($validation));
```

## 📈 **Avantages**

### **Pour les Validateurs**
- Notifications instantanées
- Liens directs aux actions
- Moins de temps de réponse

### **Pour les Demandeurs**
- Suivi en temps réel
- Confirmations rapides
- Meilleure expérience

### **Pour l'Admin**
- Statistiques détaillées
- Monitoring des envois
- Optimisation des coûts

## 🔍 **Monitoring**

### **Logs**
```php
Log::info('SMS envoyé avec succès', [
    'to' => $notifiable->telephone,
    'template' => $template,
    'provider' => $result['provider']
]);
```

### **Alertes**
- Échec d'envoi > 5%
- Provider indisponible
- Coût anormal

## ✅ **Validation**

### **Tests à effectuer**
1. Workflow complet avec SMS
2. Templates corrects
3. Statistiques fonctionnelles
4. Gestion des erreurs

### **KPIs**
- Taux de livraison SMS > 95%
- Temps de réponse < 2h
- Coût par notification < 50 FCFA

---

**L'intégration SMS dans les workflows garantit une communication instantanée et une meilleure réactivité des équipes de validation.**
