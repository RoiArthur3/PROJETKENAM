# Intégration Hikvision ISAPI pour Pointage Automatique

## ✅ Ce qui est déjà implémenté

### 1. Webhook (Event Push) - **RECOMMANDÉ**
- **Endpoint** : `POST /api/facial/events`
- **Authentification** : Header `X-Device-Token: <token>`
- **Reçoit en temps réel** les événements du terminal Hikvision
- **Support complet** : reconnaissance faciale, badge, etc.
- **Normalisation automatique** des payloads Hikvision variés

### 2. API ISAPI Pull (optionnel)
- **Endpoint** : `GET /api/hikvision/events/{deviceId}`
- **Récupère les événements** depuis le terminal Hikvision
- **Endpoint de test** : `GET /api/hikvision/test/{deviceId}`
- **Endpoint d'abonnement** : `POST /api/hikvision/subscribe/{deviceId}`

---

## 🏗️ Architecture complète

```
Terminal Hikvision → Event Push → POST /api/facial/events → Laravel → Base de données → Dashboard RH
```

---

## 📡 Configuration Hikvision (Webhook)

### 1. Ajouter le terminal dans Laravel
- URL : `/rh/facial-devices/create`
- Remplir : IP, port, identifiants Hikvision
- Copier le `api_token` généré

### 2. Configurer le webhook sur le terminal Hikvision
- **URL du webhook** : `http://votre-domaine/api/facial/events`
- **Header** : `X-Device-Token: <token_copié>`
- **Événements** : AccessControl, FaceRecognition, Card

### 3. Exemple de payload Hikvision reçu
```json
{
  "EventNotificationAlert": {
    "ipAddress": "192.168.1.100",
    "AccessControllerEvent": {
      "employeeNoString": "EMP001",
      "name": "Jean Dupont",
      "dateTime": "2026-03-13T08:30:15+01:00",
      "majorEventType": "Access",
      "minorEventType": "FaceRecognition",
      "inAndOutFlag": "1",
      "faceScore": 95.5
    }
  }
}
```

---

## 🔌 Endpoints API disponibles

### Webhook (Event Push)
```bash
POST /api/facial/events
Headers:
  X-Device-Token: <token>
  Content-Type: application/json

Body: payload Hikvision (JSON ou XML)
```

### Pull ISAPI
```bash
# Récupérer les événements
GET /api/hikvision/events/{deviceId}

# Tester la connexion
GET /api/hikvision/test/{deviceId}

# S'abonner au push
POST /api/hikvision/subscribe/{deviceId}
{
  "callback_url": "http://votre-domaine/api/facial/events"
}
```

---

## 📊 Données extraites automatiquement

| Champ Hikvision | Champ Laravel | Description |
|------------------|--------------|-------------|
| `employeeNoString` | `employee_code` | Matricule employé |
| `name` | `employee_name` | Nom complet |
| `dateTime` | `event_time` | Heure événement |
| `majorEventType` | `event_type` | Type événement |
| `inAndOutFlag` | `direction` | Entry/Exit |
| `faceScore` | `confidence` | Score confiance |
| `deviceID` | `device_serial` | Série terminal |

---

## 🚀 Déploiement rapide

### Étape 1 : Ajouter le terminal
```bash
curl -X POST http://votre-app.com/rh/facial-devices \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Terminal Principal",
    "serial_number": "HIK-123456",
    "ip_address": "192.168.1.100",
    "port": 80,
    "protocol": "http",
    "username": "admin",
    "password": "password"
  }'
```

### Étape 2 : Configurer le webhook Hikvision
Dans l'interface Hikvision :
- Configuration → Network → Advanced → Event Notification
- URL : `http://votre-app.com/api/facial/events`
- Header : `X-Device-Token: <token_étape_1>`
- Activer les événements de contrôle d'accès

### Étape 3 : Vérifier en temps réel
- Visiter `/rh/facial-pointage/dashboard`
- Les pointages apparaissent automatiquement

---

## ✅ Validation automatique

- **Pointage créé** → `statut = 'present'`
- **Validé automatiquement** → `validated_at = now()`
- **Entrée** → `heure_arrivee` renseignée
- **Sortie** → `heure_depart` renseignée
- **Doublons ignorés** (fenêtre 5 secondes)

---

## 📋 Checklist déploiement

- [ ] Ajouter le terminal dans `/rh/facial-devices/create`
- [ ] Configurer le webhook Hikvision vers `/api/facial/events`
- [ ] Ajouter le header `X-Device-Token` avec le token généré
- [ ] Activer les événements AccessControl/FaceRecognition
- [ ] Tester avec un employé devant le terminal
- [ ] Vérifier les pointages dans `/rh/facial-pointage/dashboard`

---

## 🔧 Support

- **Dashboard** : `/rh/facial-pointage/dashboard`
- **Terminaux** : `/rh/facial-devices`
- **Logs** : `storage/logs/laravel.log`
- **Monitoring** : Les terminaux affichent leur statut en ligne/hors ligne
