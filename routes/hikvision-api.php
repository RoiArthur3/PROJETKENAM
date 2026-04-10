<?php

use Illuminate\Support\Facades\Route;

// Routes API pour synchronisation Hikvision
Route::prefix('hikvision')->name('api.hikvision.')->middleware(['auth'])->group(function () {
    Route::post('/sync-employees', function() {
        try {
            $syncService = app(\App\Services\HikvisionFaceSyncService::class);
            $results = $syncService->syncAllPendingEmployees();

            return response()->json([
                'success' => true,
                'message' => 'Synchronisation terminée',
                'results' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    })->name('sync-employees');

    // Test de reconnaissance faciale
    Route::get('/facial-test', function() {
        return view('test.facial-recognition');
    })->name('facial-test');

    // Test simple de caméra
    Route::get('/camera-test', function() {
        return view('test.camera-simple');
    })->name('camera-test');

    Route::post('/test-env', function() {
        try {
            // Test de connexion à la caméra
            $ip = env('HIKVISION_IP', '192.168.1.70');
            $user = env('HIKVISION_USER', 'admin');
            $pass = env('HIKVISION_PASS', 'Arthur@752');

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://{$ip}/ISAPI/System/status");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_USERPWD, $user . ':' . $pass);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $success = $httpCode === 200;

            return response()->json([
                'success' => $success,
                'message' => $success ? 'Connexion Hikvision réussie' : 'Échec de connexion',
                'ip' => $ip,
                'http_code' => $httpCode
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    })->name('test-env');

    Route::get('/devices', function() {
        try {
            $devices = \App\Models\FacialDevice::where('is_active', true)->get();

            return response()->json([
                'success' => true,
                'devices' => $devices
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    })->name('devices');
});
