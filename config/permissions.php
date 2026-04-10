<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Permissions par rôle
    |--------------------------------------------------------------------------
    |
    | Définit les permissions pour chaque rôle dans l'application
    |
    */

    'roles' => [
        'admin' => ['*'], // Admin a toutes les permissions
        'superadmin' => ['*'], // Super admin a toutes les permissions
        'tresorerie' => [
            'dashboard.view',
            'tresorerie.view',
            'tresorerie.create',
            'tresorerie.edit',
            'tresorerie.delete',
            'operations.view',     // Accès aux opérations
            'validations.view',   // Accès aux validations
            'services.view',     // Accès aux services
        ],
        'commercial' => [
            'dashboard.view',
            'commercial.view',
            'commercial.create',
            'commercial.edit',
            'operations.view',     // Accès aux opérations
            'validations.view',   // Accès aux validations
            'services.view',     // Accès aux services
        ],
        'rh' => [
            'dashboard.view',
            'rh.view',
            'rh.create',
            'rh.edit',
            'operations.view',     // Accès aux opérations
            'validations.view',   // Accès aux validations
            'services.view',     // Accès aux services
        ],
        'comptabilite' => [
            'dashboard.view',
            'comptabilite.view',
            'comptabilite.create',
            'comptabilite.edit',
            'comptabilite.delete',
            'operations.view',     // Accès aux opérations
            'validations.view',   // Accès aux validations
            'services.view',     // Accès aux services
        ],
        'moderator' => [
            'operations.view',
            'operations.create',
            'validations.view',   // Accès aux validations
            'services.view',     // Accès aux services
        ],
        'agent' => [
            'operations.view',
            'operations.create',
            'validations.view',   // Accès aux validations
            'services.view',     // Accès aux services
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Modules par rôle
    |--------------------------------------------------------------------------
    |
    | Définit les modules accessibles pour chaque rôle
    |
    */
    'modules' => [
        'admin' => ['*'], // Admin a accès à tous les modules
        'tresorerie' => [
            'dashboard',
            'tresorerie',
            'approvisionnements',
            'depenses',
            'operations',        // Accès aux opérations
            'validations',      // Accès aux validations
        ],
        'commercial' => [
            'dashboard',
            'commercial',
            'clients',
            'ventes',
            'rapports',
            'operations',        // Accès aux opérations
            'validations',      // Accès aux validations
        ],
        'rh' => [
            'dashboard',
            'rh',
            'employes',
            'conges',
            'formations',
            'operations',        // Accès aux opérations
            'validations',      // Accès aux validations
        ],
        'comptabilite' => [
            'dashboard',
            'accounting',
            'comptabilite',
            'factures',
            'depenses',
            'paiements',
            'operations',        // Accès aux opérations
            'validations',      // Accès aux validations
        ],
        'moderator' => [
            'operations',
            'accounting',
            'comptabilite',
            'validations',      // Accès aux validations
        ],
        'agent' => [
            'operations',        // Accès aux opérations
            'validations',      // Accès aux validations
        ],
    ],
];
