<?php

return [
    'enabled' => env('WHATSAPP_WEBJS_ENABLED', false),
    'api_url' => env('WHATSAPP_WEBJS_API_URL', ''),
    'token' => env('WHATSAPP_WEBJS_TOKEN', ''),
    'session' => env('WHATSAPP_WEBJS_SESSION', 'default'),
    'timeout' => env('WHATSAPP_WEBJS_TIMEOUT', 20),
];
