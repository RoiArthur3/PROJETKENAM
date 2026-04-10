<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recette extends Model
{
    protected $fillable = [
        'reference',
        'libelle',
        'montant',
        'date_recette',
        'categorie',
        'client_id',
        'caisse_id',
        'encaissement_id',
        'payment_id',
        'mode_paiement',
        'statut',
        'description',
        'created_by',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_recette' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function caisse(): BelongsTo
    {
        return $this->belongsTo(Caisse::class);
    }

    public function encaissement(): BelongsTo
    {
        return $this->belongsTo(Encaissement::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
