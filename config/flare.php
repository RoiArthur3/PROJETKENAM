<?php

use Spatie\FlareClient\FlareMiddleware\AddGitInformation;
use Spatie\FlareClient\FlareMiddleware\RemoveRequestIp;
use Spatie\FlareClient\FlareMiddleware\CensorRequestBodyFields;
use Spatie\FlareClient\FlareMiddleware\CensorRequestHeaders;
use Spatie\LaravelIgnition\FlareMiddleware\AddDumps;
use Spatie\LaravelIgnition\FlareMiddleware\AddEnvironmentInformation;
use Spatie\LaravelIgnition\FlareMiddleware\AddExceptionHandledStatus;
use Spatie\LaravelIgnition\FlareMiddleware\AddExceptionInformation;
use Spatie\LaravelIgnition\FlareMiddleware\AddJobs;
use Spatie\LaravelIgnition\FlareMiddleware\AddLogs;
use Spatie\LaravelIgnition\FlareMiddleware\AddQueries;
use Spatie\LaravelIgnition\FlareMiddleware\AddContext;
use Spatie\LaravelIgnition\FlareMiddleware\AddNotifierName;

return [
    /*
    |
    |--------------------------------------------------------------------------
    | Flare API key
    |--------------------------------------------------------------------------
    |
    | Specify Flare's API key below to enable error reporting to the service.
    |
    | More info: https://flareapp.io/docs/general/projects
    |
    */

    'key' => null, // Désactivé - plus d'envoi d'erreurs

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | These middleware will modify the contents of the report sent to Flare.
    |
    */

    'flare_middleware' => [
        // Désactivé - plus de middleware
    ],

    /*
    |--------------------------------------------------------------------------
    | Reporting log statements
    |--------------------------------------------------------------------------
    |
    | If this setting is `false` log statements won't be sent as events to Flare,
    | no matter which error level you specified in the Flare log channel.
    |
    */

    'send_logs_as_events' => false,
];
