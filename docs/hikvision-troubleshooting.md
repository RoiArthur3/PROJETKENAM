# Guide de Dépannage Hikvision ISAPI

## 🔍 **Diagnostic de Connexion**

### **Problème identifié**
```
❌ Erreur: Failed to connect to 192.168.1.70 port 80 after 10015ms: Timeout was reached
```

### **Causes possibles**

#### 1. **Pare-feu/Réseau**
- Le port 80 est bloqué sur le réseau
- Le terminal Hikvision n'est pas accessible depuis cette machine
- Configuration réseau incorrecte

#### 2. **Terminal Hikvision**
- L'appareil est éteint ou déconnecté
- Service HTTP désactivé sur le terminal
- Adresse IP incorrecte

#### 3. **Configuration ISAPI**
- L'API ISAPI n'est pas activée
- Version firmware incompatible
- Permissions insuffisantes

---

## 🛠️ **Solutions à tester**

### **Étape 1: Vérification réseau**
```bash
# Test de connectivité de base
ping 192.168.1.70

# Test de port avec PowerShell
Test-NetConnection -ComputerName 192.168.1.70 -Port 80

# Scan de ports
nmap -p 80,443,554,8000 192.168.1.70
```

### **Étape 2: Tests avec différents ports**
```bash
# Port 80 (HTTP par défaut)
curl -u admin:Arthur@752 http://192.168.1.70:80/ISAPI/System/deviceInfo

# Port 8080 (alternatif)
curl -u admin:Arthur@752 http://192.168.1.70:8080/ISAPI/System/deviceInfo

# Port 443 (HTTPS)
curl -u admin:Arthur@752 -k https://192.168.1.70:443/ISAPI/System/deviceInfo
```

### **Étape 3: Configuration via interface web**
1. **Accéder au terminal**: `http://192.168.1.70` dans un navigateur
2. **Vérifier**:
   - Service HTTP activé
   - Port HTTP configuré
   - API ISAPI activée
   - Permissions utilisateur

### **Étape 4: Tests avec identifiants alternatifs**
```php
// Essayer différents comptes
$credentials = [
    ['admin', 'Arthur@752'],
    ['admin', 'admin'],
    ['admin', '12345'],
    ['admin', ''],
    ['root', 'root'],
    ['user', 'user']
];
```

---

## 📋 **Commandes Laravel disponibles**

### **Commande de test**
```bash
# Test avec variables d'environnement
php artisan hikvision:fetch-events

# Test avec paramètres directs
php artisan hikvision:fetch-events \
  --ip=192.168.1.70 \
  --url=/ISAPI/AccessControl/AcsEvent \
  --username=admin \
  --password=Arthur@752 \
  --port=80 \
  --protocol=http \
  --timeout=30
```

### **Test avec device enregistré**
```bash
# Si le device est enregistré en base
php artisan hikvision:fetch-events --device=1
```

---

## 🔧 **Configuration recommandée**

### **Variables d'environnement (.env)**
```env
HIKVISION_IP=192.168.1.70
HIKVISION_USER=admin
HIKVISION_PASS=Arthur@752
HIKVISION_PORT=80
HIKVISION_PROTOCOL=http
HIKVISION_TIMEOUT=30
```

### **Configuration terminal Hikvision**
1. **Activer ISAPI**:
   - Menu: Configuration → Network → Advanced Settings → Integration Protocol
   - Cocher "Enable ISAPI"
   - Redémarrer le service

2. **Vérifier HTTP**:
   - Menu: Configuration → Network → Basic Settings
   - HTTP Port: 80 (ou autre port)
   - HTTPS Port: 443

3. **Permissions utilisateur**:
   - Menu: Configuration → User → User Management
   - Droits: "Operation" → "Remote Config"
   - Droits: "Operation" → "Control"

---

## 🌐 **URLs ISAPI à tester**

### **Endpoints de base**
```
/ISAPI/System/deviceInfo
/ISAPI/AccessControl/AcsEvent
/ISAPI/AccessControl/EventLog/Info
/ISAPI/Intelligent/FDLib/FaceDataRecord
/ISAPI/Event/notification/subscribe
```

### **Endpoints alternatives**
```
/ISAPI/AccessControl/RemoteControl/door/1
/ISAPI/Streaming/channels/1
/ISAPI/ContentMgmt/InputProxy/channels/1
```

---

## 📊 **Monitoring et Logs**

### **Logs Laravel**
```bash
# Voir les logs d'erreurs
tail -f storage/logs/laravel.log | grep Hikvision

# Logs spécifiques
grep -i "hikvision" storage/logs/laravel.log
```

### **Logs système**
```bash
# Logs réseau
netstat -an | grep 192.168.1.70

# Logs firewall (Windows)
Get-WinEvent -FilterHashtable @{LogName='Security'; ID=5156} | Where-Object {$_.Message -like "*192.168.1.70*"}
```

---

## 🚨 **Actions immédiates**

### **1. Vérifier l'accessibilité**
```php
// Script de test simple
<?php
$ip = '192.168.1.70';
$ports = [80, 8080, 443, 554];

foreach ($ports as $port) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://$ip:$port");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Port $port: " . ($httpCode > 0 ? "✅ Ouvert" : "❌ Fermé") . "\n";
}
?>
```

### **2. Configuration réseau**
- Vérifier que la machine peut joindre `192.168.1.70`
- Désactiver temporairement le pare-feu Windows
- Tester depuis une autre machine du même réseau

### **3. Interface web Hikvision**
- Accéder à `http://192.168.1.70` via navigateur
- Vérifier l'état des services
- Consulter les logs du terminal

---

## 📞 **Support**

### **Informations à collecter**
1. **Modèle terminal**: DS-K1T342 ou autre
2. **Version firmware**: Via interface web
3. **Configuration réseau**: IP, masque, gateway
4. **Logs système**: Erreurs spécifiques
5. **Environnement**: Windows/Linux, version PHP

### **Contact support**
- Fournir les résultats des tests ci-dessus
- Captures d'écran de l'interface web
- Logs complets de Laravel

---

**⚠️ Note**: Le timeout de connexion indique probablement un problème réseau ou de configuration du terminal, pas un problème de code PHP.
