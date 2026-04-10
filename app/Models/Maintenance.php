<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Maintenance extends Model
{
    protected $fillable = [
        'reference',
        'vehicule_id',
        'type',
        'date_maintenance',
        'kilometrage',
        'technicien_id',
        'technicien_nom',
        'cout',
        'statut',
        'description',
        'pieces_utilisees',
        'duree',
        'prochaine_echeance',
        'resultat',
        'resultat_notes',
        'completed_at',
    ];

    protected $casts = [
        'date_maintenance' => 'date',
        'prochaine_echeance' => 'date',
        'completed_at' => 'datetime',
        'cout' => 'decimal:2',
    ];

    public function vehicule(): BelongsTo
    {
        return $this->belongsTo(Vehicule::class, 'vehicule_id');
    }
}
