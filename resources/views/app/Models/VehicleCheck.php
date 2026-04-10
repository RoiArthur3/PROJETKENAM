<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleCheck extends Model
{
    protected $fillable = [
        'numero_fiche',
        'vehicle_id',
        'user_id',
        'date_check',
        'type_check',
        'items_checked',
        'etat_general',
        'observations',
        'recommandations',
        'valide',
        'valide_par',
        'date_validation',
    ];

    protected $casts = [
        'date_check' => 'date',
        'date_validation' => 'datetime',
        'items_checked' => 'array',
        'valide' => 'boolean',
    ];

    /**
     * Relation avec le véhicule
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Relation avec le contrôleur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec le valideur
     */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par');
    }
}
