<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

function methodBody(string $class, string $method): ?string
{
    if (!class_exists($class) || !method_exists($class, $method)) {
        return null;
    }

    $reflection = new ReflectionMethod($class, $method);
    $file = $reflection->getFileName();
    if (!$file || !is_file($file)) {
        return null;
    }

    $lines = file($file);
    if ($lines === false) {
        return null;
    }

    $start = $reflection->getStartLine() - 1;
    $length = $reflection->getEndLine() - $reflection->getStartLine() + 1;

    return implode('', array_slice($lines, $start, $length));
}

function hasDbUsage(?string $body): bool
{
    if (!$body) {
        return false;
    }

    $patterns = [
        'DB::',
        '::query(',
        '::where(',
        '::count(',
        '::sum(',
        '->where(',
        '->count(',
        '->sum(',
        '->get(',
        '->first(',
        '->paginate(',
        '->with(',
        '->join(',
    ];

    foreach ($patterns as $pattern) {
        if (str_contains($body, $pattern)) {
            return true;
        }
    }

    return false;
}

function findRouteByUrl(string $url)
{
    $uri = trim(parse_url($url, PHP_URL_PATH) ?? '', '/');

    foreach (Route::getRoutes() as $route) {
        if (trim($route->uri(), '/') === $uri) {
            return $route;
        }
    }

    return null;
}

try {
    DB::select('SELECT 1');
    echo "DB connection: OK\n\n";
} catch (Throwable $e) {
    echo "DB connection: FAILED - {$e->getMessage()}\n";
    exit(1);
}

$submodulesConfig = config('submodules', []);
$results = [];
$total = 0;
$ok = 0;

foreach ($submodulesConfig as $moduleKey => $module) {
    $submodules = $module['submodules'] ?? [];

    foreach ($submodules as $subKey => $submodule) {
        $total++;
        $targetType = isset($submodule['route']) ? 'route' : (isset($submodule['url']) ? 'url' : 'none');
        $target = $submodule['route'] ?? ($submodule['url'] ?? '');
        $status = 'KO';
        $details = '';

        try {
            $route = null;

            if ($targetType === 'route') {
                if (Route::has($target)) {
                    $route = Route::getRoutes()->getByName($target);
                } else {
                    $details = 'Route name introuvable';
                }
            } elseif ($targetType === 'url') {
                $route = findRouteByUrl($target);
                if (!$route) {
                    $details = 'URL non mapee a une route';
                }
            } else {
                $details = 'Aucune route/url definie';
            }

            if ($route) {
                $action = $route->getActionName();

                if ($action === 'Closure') {
                    $status = 'WARN';
                    $details = 'Route closure (DB non detectee automatiquement)';
                } else {
                    [$class, $method] = explode('@', $action);
                    $body = methodBody($class, $method);

                    if ($body === null) {
                        $status = 'WARN';
                        $details = "Action introuvable: {$action}";
                    } elseif (hasDbUsage($body)) {
                        $status = 'OK';
                        $details = "{$action}";
                    } else {
                        $status = 'WARN';
                        $details = "Aucun acces DB detecte dans {$action}";
                    }
                }
            }
        } catch (Throwable $e) {
            $status = 'KO';
            $details = $e->getMessage();
        }

        if ($status === 'OK') {
            $ok++;
        }

        $results[] = [
            'module' => $moduleKey,
            'submodule' => $subKey,
            'name' => $submodule['name'] ?? $subKey,
            'target' => $target,
            'status' => $status,
            'details' => $details,
        ];
    }
}

echo "Submodules checks: {$total}\n";
echo "Connected/DB-detected: {$ok}\n\n";

foreach ($results as $row) {
    echo sprintf(
        "[%s] %s::%s | %s | %s\n",
        $row['status'],
        $row['module'],
        $row['submodule'],
        $row['target'],
        $row['details']
    );
}

echo "\nSummary:\n";
echo "OK={$ok} / TOTAL={$total}\n";
