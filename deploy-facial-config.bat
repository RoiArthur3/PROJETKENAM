@echo off
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
php artisan route:list | findstr "facial"
php artisan route:list | findstr "camera"

echo.
echo 4. Creation des repertoires de stockage:
if not exist "storage\app\facial_recognition" mkdir "storage\app\facial_recognition"
if not exist "storage\app\faces_database" mkdir "storage\app\faces_database"

echo.
echo === Deploiement termine ===
echo.
echo URLs de test en production:
echo https://ksl.kenamservices.net/hikvision/facial-test
echo https://ksl.kenamservices.net/hikvision/camera-test
echo.

pause
