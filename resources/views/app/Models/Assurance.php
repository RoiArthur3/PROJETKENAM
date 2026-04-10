<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assurance extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'numero_police',
        'assureur',
        'date_debut',
        'date_fin',
        'prime_annuelle',
        'statut',
        'notes',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'prime_annuelle' => 'decimal:2',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicule::class, 'vehicle_id');
    }

    public function getStatutLabelAttribute(): string
    {
        return match($this->statut) {
            'active' => 'Active',
            'a_renouveler' => 'À renouveler',
            'expiree' => 'Expirée',
            default => ucfirst($this->statut ?? 'Inconnu'),
        };
    }
}
