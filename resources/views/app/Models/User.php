<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

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
    ];

    /**
     * Check if user has a specific role
     */
    public function hasRole($role)
    {
        return $this->role === $role;
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

    /**
     * Vérifier si l'utilisateur peut accéder à un module
     *
     * Règles:
     * - Superadmin: accès à TOUS les modules
     * - Admin: accès à tout SAUF /dashboard
     * - Moderateur: operations + validations + modules cochés
     * - Agent: uniquement operations + validations
     */
    public function canAccessModule($module)
    {
        // Superadmin : accès à TOUT sans exception
        if ($this->hasRole('superadmin')) {
            return true;
        }

        // Admin : accès à tout y compris le dashboard
        if ($this->hasRole('admin')) {
            return true;  // ✅ Admin a accès à tout y compris dashboard
        }

        // Moderateur : operations + validations + modules cochés
        if ($this->hasRole('moderator') || $this->hasRole('moderateur')) {
            // Toujours accès aux opérations et validations
            if (in_array($module, ['operations', 'validations'])) {
                return true;
            }
            // Vérifier les modules cochés (permissions spécifiques)
            return $this->hasModulePermission($module);
        }

        // Agent : uniquement operations + validations
        if ($this->hasRole('agent')) {
            return in_array($module, ['operations', 'validations']);
        }

        // Par défaut, pas d'accès
        return false;
    }

    /**
     * Vérifier si l'utilisateur a une permission de module spécifique
     * (utilisé pour les modérateurs avec modules cochés)
     */
    public function hasModulePermission($module)
    {
        // Mapper les noms de modules sidebar (français) vers les noms de permissions (anglais)
        $moduleMap = [
            'tresorerie' => 'treasury',
            'comptabilite' => 'accounting',
            'rh' => 'hr',
            'fournisseurs' => 'suppliers',
            'materiel' => 'fleet',
            'magasin' => 'warehouse',
            'entrepots' => 'warehouse',
        ];

        $permissionModule = $moduleMap[$module] ?? $module;

        $exists = DB::table('model_has_permissions')
            ->join('permissions', 'model_has_permissions.permission_id', '=', 'permissions.id')
            ->where('model_has_permissions.model_id', $this->id)
            ->where('model_has_permissions.model_type', 'App\\Models\\User')
            ->where('permissions.name', $permissionModule . '.access')
            ->exists();

        // Log temporaire pour debug en production
        \Log::info("hasModulePermission: user={$this->id}, module={$module}, mapped={$permissionModule}, permission={$permissionModule}.access, result=" . ($exists ? 'true' : 'false'));

        return $exists;
    }

    /**
     * Vérifier si l'utilisateur a accès à la trésorerie
     */
    public function canAccessTreasury()
    {
        return $this->can_access_accounting || $this->isAdmin();
    }

    /**
     * Vérifier si l'utilisateur a une permission spécifique
     */
    public function hasPermission($permission)
    {
        // L'administrateur et superadmin ont tous les droits
        if ($this->isAdmin()) {
            return true;
        }

        // Vérifier dans la table des permissions
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
