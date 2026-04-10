<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Modules de base pour le modérateur
    |--------------------------------------------------------------------------
    |
    | Modules que le modérateur a toujours accès.
    |
    */
    'base_modules' => [
        'operations',
        'validations',
    ],

    /*
    |--------------------------------------------------------------------------
    | Modules supplémentaires pour le modérateur
    |--------------------------------------------------------------------------
    |
    | Liste des modules supplémentaires que le modérateur peut avoir en plus
    | de operations et validation qui sont ses modules de base.
    |
    */
    'additional_modules' => [
        'commercial',
        'rh',
        'comptabilite',
        'tresorerie',
        'stock',
        'fournisseurs',
        'parc',
        'magasin',
        'devis',
        'agenda',
        'notifications',
        // Ajoutez ici les modules que vous voulez donner au modérateur
    ],

    /*
    |--------------------------------------------------------------------------
    | Permissions du modérateur
    |--------------------------------------------------------------------------
    |
    | Définit si le modérateur peut avoir des modules supplémentaires
    | ou s'il est limité à operations et validation uniquement
    |
    */
    'can_have_additional_modules' => true,
];
