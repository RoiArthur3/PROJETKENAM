<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Agent extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'matricule',
        'nom',
        'prenom',
        'email',
        'password',
        'telephone',
        'service_id',
        'is_active',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Relation avec le service
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Obtenir le nom complet
     */
    public function getNomCompletAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    /**
     * Obtenir les requêtes de l'agent
     */
    public function requetes()
    {
        return $this->hasMany(Requete::class, 'agent_id');
    }

    /**
     * Vérifier si l'agent peut accéder aux requêtes
     */
    public function canAccessRequetes(): bool
    {
        return $this->is_active && $this->service_id;
    }

    /**
     * Mettre à jour la dernière connexion
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login' => now()]);
    }

    /**
     * Scope pour les agents actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour les agents par service
     */
    public function scopeByService($query, $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }
}
