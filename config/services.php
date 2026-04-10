<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'smseco' => [
        'api_key' => env('SMSECO_API_KEY'),
        'api_secret' => env('SMSECO_API_SECRET'),
        'sender_id' => env('SMSECO_SENDER_ID', 'KENAM'),
        'from_name' => env('SMS_FROM_NAME', 'KENAM SERVICES'),
        'endpoint' => 'https://api.smseco.com/v1/sms/send',
    ],

    'email_inbound' => [
        'secret' => env('EMAIL_INBOUND_SECRET'),
    ],

    'whatsapp_appro' => [
        'enabled' => env('WHATSAPP_APPRO_ENABLED', env('WHATSAPP_WEBJS_ENABLED', false)),
        'api_url' => env('WHATSAPP_APPRO_API_URL', env('WHATSAPP_WEBJS_API_URL', '')),
        'token' => env('WHATSAPP_APPRO_TOKEN', env('WHATSAPP_WEBJS_TOKEN', '')),
        'session' => env('WHATSAPP_APPRO_SESSION', 'whatsapp-appro'),
        'timeout' => env('WHATSAPP_APPRO_TIMEOUT', env('WHATSAPP_WEBJS_TIMEOUT', 20)),
    ],

];
