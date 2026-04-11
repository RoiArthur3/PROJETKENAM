<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationVehicule extends Model
{
    use HasFactory;

    protected $fillable = [
        'operation_id',
        'vehicule_id',
        'date_affectation',
        'date_fin_affectation',
        'actif',
        'notes',
    ];

    protected $casts = [
        'date_affectation' => 'date',
        'date_fin_affectation' => 'date',
        'actif' => 'boolean',
    ];

    /**
     * Obtenir l'opération associée
     */
    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

    /**
     * Obtenir le véhicule associé
     */
    public function vehicule(): BelongsTo
    {
        return $this->belongsTo(Vehicule::class);
    }

    /**
     * Scope pour les affectations actives
     */
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Scope pour les affectations en cours (non terminées)
     */
    public function scopeEnCours($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('date_fin_affectation')
              ->orWhere('date_fin_affectation', '>=', now());
        });
    }
}
