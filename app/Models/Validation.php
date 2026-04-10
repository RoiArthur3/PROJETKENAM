<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Validation extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_source',
        'record_id',
        'type',
        'titre',
        'description',
        'initiateur_id',
        'validateur_id',
        'statut',
        'commentaire',
        'date_validation',
        'workflow_data',
    ];

    protected $casts = [
        'workflow_data' => 'array',
        'date_validation' => 'datetime',
    ];

    public function initiateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiateur_id');
    }

    public function validateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validateur_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ValidationLog::class);
    }

    /**
     * Relation polymorphique vers le document source
     */
    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class, 'record_id')
            ->where('module_source', 'operations');
    }

    /**
     * Relation générique vers n'importe quel module source
     */
    public function sourceRecord()
    {
        return match($this->module_source) {
            'operations' => $this->belongsTo(Operation::class, 'record_id'),
            'parc_auto' => $this->belongsTo(\App\Models\Vehicule::class, 'record_id'),
            'rh' => $this->belongsTo(\App\Models\Agent::class, 'record_id'),
            'stock' => $this->belongsTo(\App\Models\StockMovement::class, 'record_id'),
            'comptabilite' => $this->belongsTo(\App\Models\Depense::class, 'record_id'),
            default => null
        };
    }

    public function scopeForUser($query, $user)
    {
        return $query->where(function($q) use ($user) {
            $q->where('initiateur_id', $user->id)
              ->orWhere('validateur_id', $user->id);
        });
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopeEnCours($query)
    {
        return $query->where('statut', 'en_cours');
    }

    public function getStatusColorAttribute()
    {
        return match($this->statut) {
            'en_attente' => 'warning',
            'en_cours' => 'info',
            'valide' => 'success',
            'rejete' => 'danger',
            'corrige' => 'secondary',
            default => 'secondary'
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->statut) {
            'en_attente' => 'En attente',
            'en_cours' => 'En cours',
            'valide' => 'Validé',
            'rejete' => 'Rejeté',
            'corrige' => 'À corriger',
            default => 'Inconnu'
        };
    }
}
