<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequeteHistorique extends Model
{
    use HasFactory;

    protected $table = 'requete_historiques';

    protected $fillable = [
        'requete_id',
        'user_id',
        'action',
        'ancien_statut',
        'nouveau_statut',
        'commentaire',
        'donnees_modifiees',
    ];

    protected $casts = [
        'donnees_modifiees' => 'array',
    ];

    public function requete(): BelongsTo
    {
        return $this->belongsTo(Requete::class, 'requete_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
