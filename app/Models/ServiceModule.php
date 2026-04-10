<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceModule extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'module_code',
        'can_access'
    ];

    protected $casts = [
        'can_access' => 'boolean'
    ];

    /**
     * Le service associé
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Obtenir les informations du module
     */
    public function getModuleInfo()
    {
        $modules = \App\Services\UserPermissionService::getAllModules();
        return $modules[$this->module_code] ?? null;
    }

    /**
     * Scope pour les modules accessibles
     */
    public function scopeAccessible($query)
    {
        return $query->where('can_access', true);
    }
}
