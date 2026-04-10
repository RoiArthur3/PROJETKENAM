<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Caisse;
use App\Models\User;
use App\Models\MouvementCaisse;

class Rapprochement extends Model
{
    protected $fillable = [
        'caisse_id',
        'user_id',
        'date_rapprochement',
        'solde_theorique',
        'solde_reel',
        'ecart',
        'statut',
        'commentaire',
        'reference'
    ];

    protected $casts = [
        'date_rapprochement' => 'datetime',
        'solde_theorique' => 'decimal:2',
        'solde_reel' => 'decimal:2',
        'ecart' => 'decimal:2'
    ];

    /**
     * Récupère la caisse associée au rapprochement
     */
    public function caisse()
    {
        return $this->belongsTo(Caisse::class);
    }

    /**
     * Récupère l'utilisateur qui a effectué le rapprochement
     */
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Récupère les mouvements associés au rapprochement
     */
    public function mouvements()
    {
        return $this->belongsToMany(MouvementCaisse::class, 'mouvement_rapprochement')
            ->withTimestamps();
    }
}
