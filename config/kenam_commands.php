<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Console Commands
    |--------------------------------------------------------------------------
    |
    | This file is where you may define all of your Closure based console
    | commands. Each Closure is bound to a command instance within a
    | container based approach. This allows for a great deal of flexibility.
    |
    */

    'commands' => [
        // Commands système KENAM
        \App\Console\Commands\DiagnoseSystem::class,
        \App\Console\Commands\SystemHealthCheck::class,
        \App\Console\Commands\FixCommonIssues::class,
    ],
];
