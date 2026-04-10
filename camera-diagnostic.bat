@echo off
echo === Diagnostic Caméra Windows ===
echo.

echo 1. Verification des peripheriques camera:
powershell -Command "Get-PnpDevice -Class Camera | Format-Table Status, FriendlyName -AutoSize"
echo.

echo 2. Verification des services camera:
sc query "FrameServer"
echo.

echo 3. Verification des applications utilisant la camera:
powershell -Command "Get-Process | Where-Object {$_.ProcessName -like '*camera*' -or $_.ProcessName -like '*webcam*' -or $_.ProcessName -like '*zoom*' -or $_.ProcessName -like '*teams*' -or $_.ProcessName -like '*skype*'} | Select-Object ProcessName, Id"
echo.

echo 4. Test de la camera avec PowerShell:
powershell -Command "[System.Windows.Forms.Media.Capture]::CaptureAsync()"
echo.

echo 5. Verification des permissions de confidentialite:
echo Verifiez dans: Parametres Windows -^> Confidentialite -^> Camera
echo.

pause
