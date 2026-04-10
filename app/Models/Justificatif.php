<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Justificatif extends Model
{
    protected $fillable = [
        'depense_id',
        'fichier',
        'type_fichier',
        'nom_original',
        'valide_par',
        'date_validation',
        'commentaires'
    ];

    protected $casts = [
        'date_validation' => 'datetime',
    ];

    public function depense(): BelongsTo
    {
        return $this->belongsTo(DepenseCaisse::class, 'depense_id');
    }

    public function validateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    public function getCheminFichierAttribute(): string
    {
        return storage_path('app/justificatifs/' . $this->fichier);
    }

    public function getUrlFichierAttribute(): string
    {
        return asset('storage/justificatifs/' . $this->fichier);
    }
}
