<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApprovisionnementCaisse extends Model
{
    protected $fillable = [
        'numero_operation',
        'caisse_source_id',
        'caisse_destination_id',
        'montant',
        'devise',
        'statut',
        'motif',
        'mode',
        'demandeur_id',
        'valideur_id',
        'date_validation',
        'date_decaissement',
        'date_cloture',
        'commentaire_validation',
        'commentaire_rejet',
        'notes',
        'pieces_jointes',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_validation' => 'datetime',
        'date_decaissement' => 'datetime',
        'date_cloture' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($approvisionnement) {
            $approvisionnement->numero_operation = 'APP-' . now()->format('Ymd') . '-' . strtoupper(uniqid());
        });
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(Caisse::class, 'caisse_source_id');
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Caisse::class, 'caisse_destination_id');
    }

    public function demandeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'demandeur_id');
    }

    public function validateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valideur_id');
    }

    public function depenses(): HasMany
    {
        return $this->hasMany(DepenseCaisse::class, 'approvisionnement_id');
    }

    public function getMontantDepenseAttribute()
    {
        return $this->depenses()->sum('montant');
    }

    public function getSoldeRestantAttribute()
    {
        return $this->montant - $this->montant_depense;
    }
}
