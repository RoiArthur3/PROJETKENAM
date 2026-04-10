<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Modules disponibles pour l'admin
    |--------------------------------------------------------------------------
    |
    | Liste des modules que l'admin peut voir et gérer.
    |
    */
    'modules' => [
        '*', // Accès complet par défaut
        // Ou spécifier les modules:
        // 'dashboard', 'comptabilite', 'stock', 'validations', 'commercial',
        // 'fournisseurs', 'notifications', 'operations', 'parc', 'rh',
        // 'agent', 'devis', 'parametrage', 'magasin', 'entrepots',
        // 'materiel', 'audit', 'reporting', 'analyses', 'tresorerie',
        // 'agenda', 'settings', 'checking', 'requetes'
    ],

    /*
    |--------------------------------------------------------------------------
    | Modules restreints pour l'admin
    |--------------------------------------------------------------------------
    |
    | Liste des modules que l'admin ne peut pas voir.
    | Si un module est dans cette liste, il sera masqué pour l'admin.
    |
    */
    'restricted_modules' => [
        // Exemple: 'settings', 'audit', 'reporting'
        // Décommentez les modules que vous voulez restreindre
    ],

    /*
    |--------------------------------------------------------------------------
    | Permissions de l'admin
    |--------------------------------------------------------------------------
    |
    | Définit si l'admin a accès à tous les modules par défaut
    | ou s'il doit suivre les restrictions ci-dessus
    |
    */
    'admin_has_full_access' => true,
];
