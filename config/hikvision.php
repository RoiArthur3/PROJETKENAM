<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Hikvision Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration pour la connexion aux terminaux Hikvision via ISAPI
    |
    */

    'enabled' => env('HIKVISION_ENABLED', false),

    'default_ip' => env('HIKVISION_IP', '192.168.1.70'),
    'default_port' => env('HIKVISION_PORT', 80),
    'default_protocol' => env('HIKVISION_PROTOCOL', 'http'),
    'default_user' => env('HIKVISION_USER', 'admin'),
    'default_password' => env('HIKVISION_PASSWORD', ''),
    'default_endpoint' => env('HIKVISION_ENDPOINT', '/ISAPI/AccessControl/AcsEvent'),
    'default_timeout' => env('HIKVISION_TIMEOUT', 30),
    'default_max_results' => env('HIKVISION_MAX_RESULTS', 100),

    /*
    |--------------------------------------------------------------------------
    | Hikvision Endpoints
    |--------------------------------------------------------------------------
    |
    | Liste des endpoints ISAPI disponibles pour les terminaux Hikvision
    |
    */
    'endpoints' => [
        'acs_events' => '/ISAPI/AccessControl/AcsEvent',
        'event_log' => '/ISAPI/AccessControl/EventLog/Info',
        'device_info' => '/ISAPI/System/deviceInfo',
        'face_data' => '/ISAPI/Intelligent/FDLib/FaceDataRecord',
        'event_subscribe' => '/ISAPI/Event/notification/subscribe',
        'remote_control' => '/ISAPI/AccessControl/RemoteControl/door/1',
    ],

    /*
    |--------------------------------------------------------------------------
    | Hikvision Settings
    |--------------------------------------------------------------------------
    |
    | Paramètres généraux pour l'interaction avec les terminaux Hikvision
    |
    */
    'settings' => [
        'connection_timeout' => 30,
        'read_timeout' => 60,
        'retry_attempts' => 3,
        'retry_delay' => 5,
        'enable_logging' => true,
        'verify_ssl' => false,
        'user_agent' => 'KENAM-Hikvision-Client/1.0',
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration Storage
    |--------------------------------------------------------------------------
    |
    | Stockage de la configuration utilisateur (fichier ou base de données)
    |
    */
    'storage' => [
        'driver' => 'file', // 'file' ou 'database'
        'path' => storage_path('app/hikvision_config.json'),
    ],
];
