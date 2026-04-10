#!/bin/bash

echo === Deploiement Configuration Reconnaissance Faciale ===
echo.

echo 1. Nettoyage du cache de configuration:
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo.
echo 2. Verification de la configuration:
php artisan tinker --execute="echo 'FACIAL_RECOGNITION_ENABLED: ' . config('facial_recognition.enabled') . PHP_EOL;"
php artisan tinker --execute="echo 'HIKVISION_IP: ' . env('HIKVISION_IP') . PHP_EOL;"

echo.
echo 3. Verification des routes:
php artisan route:list | grep -i "facial\|camera"

echo.
echo 4. Test de la configuration Hikvision:
curl -X POST "http://127.0.0.1:8000/hikvision/test-env" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -b "laravel_session=$(php artisan tinker --execute="echo session()->getId();" | tail -1)"

echo.
echo === Deploiement termine ===
echo.
echo URLs de test en production:
echo https://ksl.kenamservices.net/hikvision/facial-test
echo https://ksl.kenamservices.net/hikvision/camera-test
