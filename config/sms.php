<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SMS Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration pour l'envoi de SMS via NetSMSPro (revendeur SMSECO)
    |
    */

    'default' => env('SMS_PROVIDER', 'netsmspro'),

    'providers' => [
        'netsmspro' => [
            'driver' => 'netsmspro',
            'username' => env('NETSMPRO_USERNAME'),
            'password' => env('NETSMPRO_PASSWORD'),
            'sender_id' => env('NETSMPRO_SENDER_ID', 'KENAM'),
            'reseller_code' => env('NETSMPRO_RESELLER_CODE', '2656631451'),
            'base_url' => env('NETSMPRO_BASE_URL', 'https://www.netsmspro.net/api'),
        ],

        'smseco' => [
            'driver' => 'smseco',
            'api_key' => env('SMSECO_API_KEY'),
            'api_secret' => env('SMSECO_API_SECRET'),
            'sender_name' => env('SMSECO_SENDER_NAME', 'KENAM'),
            'base_url' => env('SMSECO_BASE_URL', 'https://api.smseco.com'),
        ],

        'orange' => [
            'driver' => 'orange',
            'api_key' => env('ORANGE_SMS_API_KEY'),
            'api_secret' => env('ORANGE_SMS_API_SECRET'),
            'sender_name' => env('ORANGE_SENDER_NAME', 'KENAM'),
            'base_url' => 'https://api.orange.com/smsmessaging/v1/outbound',
        ],

        'mtn' => [
            'driver' => 'mtn',
            'api_key' => env('MTN_SMS_API_KEY'),
            'api_secret' => env('MTN_SMS_API_SECRET'),
            'sender_name' => env('MTN_SENDER_NAME', 'KENAM'),
            'base_url' => 'https://api.mtn.co.ug/sms',
        ],

        'twilio' => [
            'driver' => 'twilio',
            'sid' => env('TWILIO_SID'),
            'token' => env('TWILIO_TOKEN'),
            'from' => env('TWILIO_FROM'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS Templates
    |--------------------------------------------------------------------------
    |
    | Modèles de messages SMS prédéfinis
    |
    */
    'templates' => [
        'operation_alert' => 'KENAM: Votre intervention est requise pour {reference} ({titre}) montant {montant} FCFA. Ouvrir: {url}',
        'validation_approved' => 'KENAM: Opération {reference} validée avec succès par {validateur}.',
        'validation_rejected' => 'KENAM: Opération {reference} rejetée. Motif: {motif}',
        'payment_reminder' => 'KENAM: Rappel: Paiement de {montant} FCFA dû pour {reference}. Échéance: {date}',
        'stock_alert' => 'KENAM: Alert Stock: Produit {produit} en rupture. Stock actuel: {quantite}',
        'system_maintenance' => 'KENAM: Maintenance système prévue le {date} de {heure_debut} à {heure_fin}.',

        // Templates pour workflow de validation
        'validation_required' => 'KENAM: {validateur}, validation requise pour {titre} ({module}/{type}). {description}. Voir: {url}',
        'validation_completed' => 'KENAM: {demandeur}, votre demande "{titre}" ({module}) est {statut}. {commentaire}. Détails: {url}',
        'notification' => 'KENAM: {module} - {titre}. {description}. Voir: {url}',
        'payment_notification' => 'KENAM: Paiement effectué pour {reference} ({montant} FCFA). Mode: {mode_paiement}. Date: {date_paiement}',
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS Settings
    |--------------------------------------------------------------------------
    |
    | Paramètres généraux pour l'envoi de SMS
    |
    */
    'settings' => [
        'max_length' => 160,
        'rate_limit' => 10, // messages par minute
        'retry_attempts' => 3,
        'retry_delay' => 5, // secondes
        'enable_logging' => true,
        'test_mode' => env('SMS_TEST_MODE', false),
    ],
];
