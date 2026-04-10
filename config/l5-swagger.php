<?php

return [
    'default' => 'default',
    'documentations' => [
        'default' => [
            'api' => [
                'title' => 'API de Gestion des Dépenses',
                'version' => '1.0.0',
                'description' => 'Documentation complète de l\'API pour la gestion des dépenses',
                'termsOfService' => 'https://kenam.ci/terms',
                'contact' => [
                    'name' => 'Support API',
                    'email' => 'support@kenam.ci',
                    'url' => 'https://kenam.ci/contact'
                ],
                'license' => [
                    'name' => 'Licence MIT',
                    'url' => 'https://opensource.org/licenses/MIT'
                ]
            ],
            'routes' => [
                'api' => 'api/documentation',
                'docs' => 'docs',
                'oauth2_callback' => 'api/oauth2-callback',
                'middleware' => [
                    'api' => [
                        \Illuminate\Http\Middleware\HandleCors::class,
                    ],
                    'asset' => [],
                    'docs' => [],
                    'oauth2_callback' => []
                ],
                'group_versions' => false,
            ],
            'paths' => [
                'docs' => storage_path('api-docs'),
                'docs_json' => 'api-docs.json',
                'docs_yaml' => 'api-docs.yaml',
                'annotations' => [
                    base_path('app'),
                    base_path('vendor/laravel'),
                ],
                'base' => env('L5_SWAGGER_BASE_PATH', null),
                'swagger_ui_assets_path' => env('L5_SWAGGER_UI_ASSETS_PATH', 'vendor/swagger-api/swagger-ui/dist/')
            ],
            'security' => [
                'bearerAuth' => [
                    'type' => 'http',
                    'scheme' => 'bearer',
                    'bearerFormat' => 'JWT',
                ],
            ],
            'swagger_version' => env('SWAGGER_VERSION', '3.0'),
            'proxy' => false,
            'additional_config_url' => null,
            'operations_sort' => null,
            'api_key' => null,
            'swagger_ui' => true,
            'validatorUrl' => null,
            'ui_router' => false,
            'display' => [
                'operations' => [
                    'sort' => 'method',
                    'tags' => [
                        'Dépenses',
                        'Rapports',
                        'Authentification',
                    ]
                ]
            ]
        ]
    ]
];
