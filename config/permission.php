<?php

return [
    // Legacy role shortcuts used by the app
    'roles' => [
        'admin' => ['*'],
        'tresorerie' => [
            'tresorerie.view',
            'tresorerie.caisses.manage',
            'tresorerie.approvisionnements.manage',
        ],
        'utilisateur' => [
            'dashboard.view',
        ],
    ],

    // Defaults expected by spatie/laravel-permission migrations
    'table_names' => [
        'roles' => 'roles',
        'permissions' => 'permissions',
        'model_has_permissions' => 'model_has_permissions',
        'model_has_roles' => 'model_has_roles',
        'role_has_permissions' => 'role_has_permissions',
    ],

    'column_names' => [
        'model_morph_key' => 'model_id',
        'team_foreign_key' => 'team_id',
        'role_pivot_key' => 'role_id',
        'permission_pivot_key' => 'permission_id',
    ],

    'teams' => false,

    'cache' => [
        'store' => 'default',
        'key' => 'spatie.permission.cache',
        'expiration_time' => 60 * 24,
    ],
];
