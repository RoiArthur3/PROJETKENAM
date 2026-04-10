<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): View
    {
        return view('permissions.index');
    }

    public function getAuthorizedModules(?User $user = null): array
    {
        $authenticatedUser = Auth::user();
        $user = $user instanceof User ? $user : ($authenticatedUser instanceof User ? $authenticatedUser : null);

        if (!$user) {
            return [];
        }

        $modules = config('submodules', []);
        $authorizedModules = [];

        foreach ($modules as $moduleKey => $moduleConfig) {
            if (!$user->canAccessModule($moduleKey)) {
                continue;
            }

            $moduleUrl = $this->resolveModuleUrl($user, $moduleKey, $moduleConfig);

            if (!$moduleUrl) {
                continue;
            }

            $allowedSubmodules = $user->getAllowedSubmodules($moduleKey);
            $submoduleCount = count($allowedSubmodules);

            $authorizedModules[$moduleKey] = [
                'name' => $moduleConfig['name'] ?? ucfirst($moduleKey),
                'icon' => $moduleConfig['icon'] ?? 'fas fa-th-large',
                'url' => $moduleUrl,
                'description' => $submoduleCount > 0
                    ? $submoduleCount . ' sous-module(s) autorisé(s)'
                    : 'Accès au module',
            ];
        }

        return $authorizedModules;
    }

    private function resolveModuleUrl(User $user, string $moduleKey, array $moduleConfig): ?string
    {
        $submodules = $moduleConfig['submodules'] ?? [];
        $allowedSubmodules = $user->hasRole('superadmin')
            ? array_keys($submodules)
            : $user->getAllowedSubmodules($moduleKey);

        $candidateKeys = $allowedSubmodules;

        if (empty($candidateKeys)) {
            $candidateKeys = array_keys($submodules);
        }

        usort($candidateKeys, static function (string $left, string $right): int {
            $leftScore = str_contains($left, 'dashboard') ? 0 : 1;
            $rightScore = str_contains($right, 'dashboard') ? 0 : 1;

            return $leftScore <=> $rightScore;
        });

        foreach ($candidateKeys as $submoduleKey) {
            $submodule = $submodules[$submoduleKey] ?? null;

            if (!$submodule) {
                continue;
            }

            $resolvedUrl = $this->resolveSubmoduleUrl($submodule);

            if ($resolvedUrl) {
                return $resolvedUrl;
            }
        }

        return null;
    }

    private function resolveSubmoduleUrl(array $submodule): ?string
    {
        if (!empty($submodule['route']) && Route::has($submodule['route'])) {
            return route($submodule['route']);
        }

        if (!empty($submodule['url'])) {
            return url($submodule['url']);
        }

        return null;
    }
}
