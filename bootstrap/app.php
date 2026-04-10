<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/auth.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Trust proxies pour les hébergements partagés (résout l'erreur 419)
        $middleware->trustProxies(at: '*');

        // Exclusions CSRF appliquées à la pile middleware active (Laravel 11).
        $middleware->validateCsrfTokens(except: [
            'login',
            'logout',
            'register',
            'phone-login',
            'phone-login/*',
            'api/*',
            'sanctum/*',
            'login-simple',
            'logout-simple',
        ]);

        // Activer le middleware CORS pour toutes les routes API
        $middleware->api([
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);

        // Aliases de middlewares personnalisés
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'check_role' => \App\Http\Middleware\CheckRole::class,
            'module' => \App\Http\Middleware\ModuleAccessMiddleware::class,
            'module_access' => \App\Http\Middleware\ModuleAccessMiddleware::class,
            'check_permissions' => \App\Http\Middleware\CheckPermissions::class,
            'check.requete' => \App\Http\Middleware\CheckRequeteAccess::class,
            'check.admin' => \App\Http\Middleware\CheckAdminAccess::class,
            'check.role' => \App\Http\Middleware\CheckUserRole::class,
            'superadmin.only' => \App\Http\Middleware\SuperadminOnly::class,
            'clear.user.cache' => \App\Http\Middleware\ClearUserCache::class,
            'service.auth' => \App\Http\Middleware\ServiceAuthMiddleware::class,
            'dashboard.access' => \App\Http\Middleware\CheckDashboardAccess::class, // Protège le dashboard - superadmin only
            'parametrage' => \App\Http\Middleware\ParametrageAccessMiddleware::class,
        ]);
    })
    ->withProviders([
        \App\Providers\RoleServiceProvider::class,
        \App\Providers\BladeServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (TokenMismatchException $e, $request) {
            try {
                Log::warning('CSRF TOKEN MISMATCH (419)', [
                    'method' => $request->method(),
                    'url' => $request->fullUrl(),
                    'path' => $request->path(),
                    'session_id' => $request->hasSession() ? $request->session()->getId() : null,
                    'session_token' => $request->hasSession() ? $request->session()->token() : null,
                    'request_token' => $request->input('_token'),
                    'cookie_names' => array_keys($request->cookies->all()),
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            } catch (\Throwable $logError) {
                // noop
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Le jeton de session a expiré. Rechargez la page puis réessayez.',
                ], 419);
            }

            if ($request->hasSession()) {
                $request->session()->regenerateToken();
            }

            return redirect()->back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->with('warning', 'Le formulaire a expiré ou votre page était trop ancienne. La page a été rechargée côté session, veuillez réessayer.');
        });
    })->create();
