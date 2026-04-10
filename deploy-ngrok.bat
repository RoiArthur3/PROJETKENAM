@echo off
echo === Deploiement temporaire avec ngrok ===
echo.

echo 1. Verification de ngrok:
ngrok version
echo.

echo 2. Demarrage de Laravel:
start "Laravel Server" cmd /k "cd /d %~dp0 && php artisan serve --host=127.0.0.1 --port=8000"

echo 3. Attente 3 secondes...
timeout /t 3 /nobreak

echo 4. Demarrage de ngrok:
ngrok http 127.0.0.1:8000

echo.
echo Votre application sera accessible via l'URL ngrok affichee ci-dessus
echo Exemple: https://abcd-1234.ngrok.io/test/camera
echo.

pause
