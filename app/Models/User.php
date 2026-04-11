<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'password',
        'service_id',
        'telephone',
        'whatsapp',
        'is_active',
        'contrat',
        'date_embauche',
        'salaire',
        'last_login_at',
        'photo_profil',
        // Ivorian HR Fields
        'sexe',
        'date_naissance',
        'lieu_naissance',
        'nationalite',
        'situation_matrimoniale',
        'nombre_enfants',
        'adresse_postale',
        'n_cnps',
        'n_cmu',
        'salaire_base',
        'sursalaire',
        'indemnite_transport',
        'indemnite_logement',
        'autres_primes',
        'categorie_professionnelle',
        'date_fin_contrat',
        'periode_essai',
        'modules',
        'submodules',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'can_access_dashboard' => 'boolean',
        'can_access_operations' => 'boolean',
        'can_access_hr' => 'boolean',
        'modules' => 'array',
        'submodules' => 'array',
    ];

    /**
     * Check if user has a specific role
     */
    public function hasRole($role)
    {
        $userRole = strtolower($this->role);
        $checkRole = strtolower($role);

        // Support pour les deux orthographes du rôle modérateur
        if (($checkRole === 'moderator' || $checkRole === 'moderateur') &&
            ($userRole === 'moderator' || $userRole === 'moderateur')) {
            return true;
        }
        return $userRole === $checkRole;
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole($roles)
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }
        return $this->hasRole($roles);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->hasAnyRole(['admin', 'superadmin']);
    }

    public function canAccessModule($module)
    {
        $role = strtolower((string) $this->role);

        // 1. Superadmin : accès TOTAL à tous les modules
        if ($role === 'superadmin') {
            return true;
        }

        // 2. Gestion des alias de modules
        $moduleToCheck = $module;
        if (in_array($module, ['fleet', 'parc'])) {
            $moduleToCheck = 'materiel';
        }
        if ($module === 'chantier') {
            $moduleToCheck = 'projects';
        }
        if ($module === 'achat') {
            $moduleToCheck = 'achat';
        }
        if ($module === 'fournisseurs') {
            $moduleToCheck = 'fournisseurs';
        }
        if ($module === 'magasin') {
            $moduleToCheck = 'magasin';
        }
        if ($module === 'entrepot') {
            $moduleToCheck = 'entrepot';
        }

        // 3. Admin : accès strict aux modules cochés, dashboard général seulement s'il est coché.
        // Ne doit jamais voir le module Paramètres.
        if ($role === 'admin') {
            if (in_array($moduleToCheck, ['settings', 'system', 'parametrage', 'parametre', 'parametres'], true)) {
                return false;
            }
            return $this->hasModulePermission($moduleToCheck);
        }

        // 4. Modérateurs : uniquement les modules explicitement cochés
        if ($role === 'moderator' || $role === 'moderateur') {
            if ($moduleToCheck === 'dashboard') {
                return false;
            }

            if (in_array($moduleToCheck, ['settings', 'system', 'parametrage', 'parametre', 'parametres'], true)) {
                return false;
            }

            return $this->hasModulePermission($moduleToCheck);
        }

        // 5. Agents : liste de ses propres opérations uniquement
        if ($role === 'agent') {
            // Les agents n'accèdent qu'à leurs opérations
            return $moduleToCheck === 'operations';
        }

        // 6. Cas général (Lecture de l'infrastructure de permissions : JSON + Colonnes)
        $permissions = \App\Services\UserPermissionService::getUserPermissions($this);

        return isset($permissions[$moduleToCheck]) && $permissions[$moduleToCheck] === true;
    }

    /**
     * Obtenir l'URL du premier module accessible pour l'utilisateur
     */
    public function getFirstAccessibleModuleUrl()
    {
        if ($this->hasRole('superadmin')) {
            return '/dashboard';
        }

        if ($this->hasRole('admin') && $this->canAccessModule('dashboard')) {
            return '/dashboard';
        }

        // Liste des modules prioritaires pour la redirection
        $modules = [
            'operations'   => '/operations',
            'validations'  => '/validations/pending',
            'tresorerie'   => '/tresorerie/dashboard',
            'accounting'   => '/comptabilite/dashboard',
            'hr'           => '/rh/dashboard',
            'materiel'     => '/materiel/cost-control',
            'warehouse'    => '/warehouse/dashboard',
            'commercial'   => '/commercial/dashboard',
            'achat'        => '/achat',
            'fournisseurs' => '/fournisseurs/dashboard',
            'projects'     => '/projets/dashboard',
        ];

        foreach ($modules as $module => $url) {
            if ($this->canAccessModule($module)) {
                return $url;
            }
        }

        // Par défaut, si rien n'est accessible, on tente /operations ou on laisse le middleware gérer le 403
        return '/operations';
    }

    /**
     * Vérifier si l'utilisateur a une permission de sous-module spécifique
     */
    public function canAccessSubmodule($submodule)
    {
        // Superadmin : accès à TOUT
        if ($this->hasRole('superadmin')) {
            return true;
        }

        if ($this->hasRole('admin') || $this->hasRole('moderator') || $this->hasRole('moderateur')) {
            $allowedSubmodules = is_array($this->submodules) ? $this->submodules : [];
            return in_array($submodule, $allowedSubmodules, true);
        }

        // Modérateurs et Agents : vérifier la colonne JSON submodules
        $allowedSubmodules = is_array($this->submodules) ? $this->submodules : [];
        if (in_array($submodule, $allowedSubmodules)) {
            return true;
        }

        // Fallback Agent pour opérations de base
        if ($this->hasRole('agent')) {
            $baseOps = ['operations_dashboard', 'operations_list', 'operations_create'];
            return in_array($submodule, $baseOps);
        }

        return false;
    }

    /**
     * Obtenir les sous-modules autorisés pour un module donné
     */
    public function getAllowedSubmodules($module)
    {
        $allSubmodules = config('submodules.' . $module . '.submodules', []);

        // Superadmin : tous les sous-modules
        if ($this->hasRole('superadmin')) {
            return array_keys($allSubmodules);
        }

        // Admin et Modérateur : strictement les sous-modules cochés
        if ($this->hasRole('admin') || $this->hasRole('moderator') || $this->hasRole('moderateur')) {
            $allowedSubmodules = is_array($this->submodules) ? $this->submodules : [];
            return array_values(array_intersect(array_keys($allSubmodules), $allowedSubmodules));
        }

        // Agent : uniquement les sous-modules de base d'opérations
        if ($this->hasRole('agent')) {
            if ($module === 'operations') {
                return ['operations_dashboard', 'operations_list', 'operations_create'];
            }
            return [];
        }

        // Moderateur : sous-modules cochés
        if ($this->hasRole('moderator') || $this->hasRole('moderateur')) {
            $allowedSubmodules = is_array($this->submodules) ? $this->submodules : [];
            return array_intersect(array_keys($allSubmodules), $allowedSubmodules);
        }

        return [];
    }

    /**
     * Vérifier si l'utilisateur a une permission de module spécifique
     * (utilisé pour les modérateurs avec modules cochés)
     */
    public function hasModulePermission($module)
    {
        if ($this->hasRole('superadmin')) {
            return true;
        }

        if (!($this->hasRole('admin') || $this->hasRole('moderator') || $this->hasRole('moderateur'))) {
            return false;
        }

        // Résoudre les alias de modules
        $aliases = [$module];
        if ($module === 'materiel') $aliases[] = 'fleet';
        if ($module === 'fleet')    $aliases[] = 'materiel';
        if ($module === 'hr')       $aliases[] = 'rh';
        if ($module === 'rh')       $aliases[] = 'hr';
        if ($module === 'comptabilite') $aliases[] = 'accounting';
        if ($module === 'accounting')   $aliases[] = 'comptabilite';
        if ($module === 'projects')     $aliases[] = 'chantier';
        if ($module === 'chantier')     $aliases[] = 'projects';
        if ($module === 'achat')        $aliases[] = 'fournisseurs';
        if ($module === 'fournisseurs') $aliases[] = 'achat';

        // SOURCE DE VÉRITÉ 1 : colonne JSON users.modules (cochée via UI admin)
        $jsonModules = is_array($this->modules) ? $this->modules : [];
        foreach ($aliases as $alias) {
            if (in_array($alias, $jsonModules)) {
                return true;
            }
        }

        // SOURCE DE VÉRITÉ 2 : colonne can_access_* (synchronisée avec le JSON)
        $columnMap = [
            'dashboard'    => 'can_access_dashboard',
            'tresorerie'   => 'can_access_accounting',
            'accounting'   => 'can_access_accounting',
            'comptabilite' => 'can_access_accounting',
            'hr'           => 'can_access_hr',
            'rh'           => 'can_access_hr',
            'materiel'     => 'can_access_fleet',
            'fleet'        => 'can_access_fleet',
            'operations'   => 'can_access_operations',
            'commercial'   => 'can_access_commercial',
            'warehouse'    => 'can_access_warehouse',
        ];
        foreach ($aliases as $alias) {
            $col = $columnMap[$alias] ?? null;
            if ($col && $this->$col) {
                return true;
            }
        }

        // FALLBACK : model_has_permissions Spatie (uniquement si les tables existent)
        if (!Schema::hasTable('model_has_permissions') || !Schema::hasTable('permissions')) {
            return false;
        }

        $permissionNames = array_map(fn($a) => $a . '.access', $aliases);
        return DB::table('model_has_permissions')
            ->join('permissions', 'model_has_permissions.permission_id', '=', 'permissions.id')
            ->where('model_id', $this->id)
            ->where('model_type', 'App\\Models\\User')
            ->whereIn('permissions.name', $permissionNames)
            ->exists();
    }

    /**
     * Vérifier si l'utilisateur a accès à la trésorerie
     */
    public function canAccessTreasury()
    {
        if ($this->isAdmin()) {
            return true;
        }

        // Les modérateurs ayant la permission tresorerie.access ont accès
        if ($this->role === 'moderator' || $this->role === 'moderateur') {
            return $this->hasModulePermission('tresorerie');
        }

        return (bool) $this->can_access_accounting;
    }

    /**
     * Vérifier si l'utilisateur a une permission spécifique
     */
    public function hasPermission($permission)
    {
        if ($this->hasRole('superadmin')) {
            return true;
        }

        // Si la permission suit le pattern module.access/submodule.access, utiliser la logique stricte.
        if (str_ends_with($permission, '.access')) {
            $permissionCode = substr($permission, 0, -7);
            if ($this->canAccessModule($permissionCode) || $this->canAccessSubmodule($permissionCode)) {
                return true;
            }
        }

        // Vérifier dans la table des permissions si elle existe
        if (!Schema::hasTable('model_has_permissions') || !Schema::hasTable('permissions')) {
            return false;
        }

        $userPermissions = DB::table('model_has_permissions')
            ->join('permissions', 'model_has_permissions.permission_id', '=', 'permissions.id')
            ->where('model_has_permissions.model_id', $this->id)
            ->where('model_has_permissions.model_type', 'App\\Models\\User')
            ->where('permissions.name', $permission)
            ->exists();

        return $userPermissions;
    }

    /**
     * Relations
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_user')->withTimestamps();
    }

    /**
     * Trouver un utilisateur par son téléphone
     */
    public function findForPassport($username)
    {
        return $this->where('telephone', $username)->first();
    }

    /**
     * Accessor pour imiter la relation roles de Spatie
     * Permet d'utiliser $user->roles comme une collection
     */
    public function getRolesAttribute()
    {
        return collect([
            (object)['name' => $this->role]
        ]);
    }
}
