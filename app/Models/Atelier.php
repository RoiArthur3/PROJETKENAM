<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Atelier extends Model
{
    protected $fillable = [
        'nom',
        'description',
        'vehicle_id',
        'user_id',
        'type',
        'statut',
        'date_debut',
        'date_fin',
        'cout_estime',
        'cout_reel',
        'notes',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'cout_estime' => 'decimal:2',
        'cout_reel' => 'decimal:2',
    ];

    /**
     * Relation avec le véhicule
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Relation avec l'utilisateur responsable
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
