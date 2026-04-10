<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration de la Reconnaissance Faciale
    |--------------------------------------------------------------------------
    |
    | Ce fichier contient tous les paramètres de configuration pour le
    | système de reconnaissance faciale utilisé pour le pointage et
    | l'authentification des employés.
    |
    */

    // Activation/Désactivation du système
    'enabled' => env('FACIAL_RECOGNITION_ENABLED', false),

    // Paramètres de détection
    'confidence_threshold' => env('FACIAL_CONFIDENCE_THRESHOLD', 85), // Seuil de confiance en %
    'max_face_size' => env('FACIAL_MAX_FACE_SIZE', 500), // Taille maximale des visages en pixels
    'min_face_size' => env('FACIAL_MIN_FACE_SIZE', 100), // Taille minimale des visages en pixels

    // Modèle de reconnaissance
    'model' => env('FACIAL_RECOGNITION_MODEL', 'face_net'), // face_net, arc_face, dlib

    // Configuration de la caméra
    'camera_source' => env('CAMERA_SOURCE', 'webcam'), // webcam, ip_camera, usb
    'camera_url' => env('CAMERA_URL', ''), // URL pour caméra IP
    'resolution' => env('CAMERA_RESOLUTION', '640x480'), // 640x480, 1280x720, 1920x1080
    'fps' => env('CAMERA_FPS', 30), // Images par seconde
    'quality' => env('CAMERA_QUALITY', 90), // Qualité d'image (1-100)

    // Sécurité
    'live_detection' => env('FACIAL_LIVE_DETECTION', true), // Détection de vivacité
    'anti_spoofing' => env('FACIAL_ANTI_SPOOFING', true), // Protection contre photos/videos

    // Performance
    'acceleration' => env('FACIAL_ACCELERATION', 'none'), // none, cuda, opencl, mps
    'batch_processing' => env('FACIAL_BATCH_PROCESSING', false), // Traitement par lot
    'max_concurrent_detections' => env('FACIAL_MAX_CONCURRENT', 4),

    // Stockage
    'save_images' => env('FACIAL_SAVE_IMAGES', false), // Sauvegarder les images de reconnaissance
    'retention_days' => env('FACIAL_RETENTION_DAYS', 30), // Durée de rétention des images
    'storage_path' => env('FACIAL_STORAGE_PATH', storage_path('app/facial_recognition')),

    // Base de données des visages
    'faces_database_path' => env('FACIAL_FACES_DB_PATH', storage_path('app/faces_database')),
    'embeddings_dimension' => env('FACIAL_EMBEDDINGS_DIM', 512), // Dimension des embeddings

    // Pointage
    'pointage_tolerance' => env('FACIAL_POINTAGE_TOLERANCE', 5), // Tolérance en minutes
    'auto_pointage' => env('FACIAL_AUTO_POINTAGE', false), // Pointage automatique
    'require_confirmation' => env('FACIAL_REQUIRE_CONFIRMATION', true), // Confirmation requise

    // Notifications
    'notification_on_failure' => env('FACIAL_NOTIFY_FAILURE', true),
    'notification_on_success' => env('FACIAL_NOTIFY_SUCCESS', false),
    'admin_notification_email' => env('FACIAL_ADMIN_EMAIL', null),

    // API
    'api_timeout' => env('FACIAL_API_TIMEOUT', 30), // Timeout en secondes
    'max_retries' => env('FACIAL_MAX_RETRIES', 3),
    'retry_delay' => env('FACIAL_RETRY_DELAY', 1000), // Délai en millisecondes

    // Logging
    'log_level' => env('FACIAL_LOG_LEVEL', 'info'), // debug, info, warning, error
    'log_failed_attempts' => env('FACIAL_LOG_FAILED', true),
    'log_successful_attempts' => env('FACIAL_LOG_SUCCESS', false),

    // Maintenance
    'auto_cleanup' => env('FACIAL_AUTO_CLEANUP', true), // Nettoyage automatique
    'cleanup_interval' => env('FACIAL_CLEANUP_INTERVAL', 24), // Heures
    'backup_faces_db' => env('FACIAL_BACKUP_DB', true), // Sauvegarder la base de visages

    // Développement
    'debug_mode' => env('FACIAL_DEBUG', false),
    'show_detection_box' => env('FACIAL_SHOW_BOX', true),
    'show_confidence_score' => env('FACIAL_SHOW_CONFIDENCE', true),
];
