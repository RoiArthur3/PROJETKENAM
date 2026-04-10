<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prospect extends Model
{
    protected $fillable = [
        'nom',
        'email',
        'telephone',
        'adresse',
        'entreprise',
        'source',
        'statut',
        'notes',
        'valeur_potentielle',
        'date_contact',
        'user_id',
    ];

    protected $casts = [
        'date_contact' => 'date',
        'valeur_potentielle' => 'decimal:2',
    ];

    /**
     * Relation avec l'utilisateur qui gère le prospect
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
