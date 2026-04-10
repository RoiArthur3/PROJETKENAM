<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration SMTP DataSNF
    |--------------------------------------------------------------------------
    |
    | Configuration personnalisée pour le serveur mail DataSNF
    | avec les paramètres fournis par l'utilisateur
    |
    */

    'mailers' => [
        'datasnf' => [
            'transport' => 'smtp',
            'host' => 'mail.datasnf.net',
            'port' => 465,
            'encryption' => 'ssl',
            'username' => 'test@datasnf.net',
            'password' => 'Arthur123456*',
            'timeout' => null,
            'auth_mode' => null,
        ],
    ],

    'from' => [
        'address' => 'test@datasnf.net',
        'name' => 'KENAM SERVICES',
    ],

    /*
    |--------------------------------------------------------------------------
    | Informations de connexion supplémentaires
    |--------------------------------------------------------------------------
    */
    
    'datasnf' => [
        'smtp' => [
            'host' => 'mail.datasnf.net',
            'port' => 465,
            'encryption' => 'ssl',
        ],
        'imap' => [
            'host' => 'mail.datasnf.net',
            'port' => 993,
            'encryption' => 'ssl',
        ],
        'pop3' => [
            'host' => 'mail.datasnf.net',
            'port' => 995,
            'encryption' => 'ssl',
        ],
    ],
];
