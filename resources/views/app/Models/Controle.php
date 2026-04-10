<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Controle extends Model
{
    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicule_id',
        'user_id',
        'date_controle',
        'type_controle',
        'commentaires',
        'resultat',
        'details',
        'statut_soumission',
        'commentaire_soumission',
        'soumis_le',
        'kilometrage',
        'prochain_controle'
    ];

    /**
     * Les attributs qui doivent être transformés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_controle' => 'date',
        'soumis_le' => 'datetime',
        'prochain_controle' => 'date',
        'details' => 'array',
    ];
    
    /**
     * Les statuts de soumission possibles.
     */
    public const STATUTS = [
        'brouillon' => 'Brouillon',
        'soumis' => 'Soumis',
        'en_cours' => 'En cours de traitement',
        'traite' => 'Traité',
        'annule' => 'Annulé',
    ];
    
    /**
     * Les services associés à ce contrôle.
     */
    public function services()
    {
        return $this->belongsToMany(Service::class)
            ->withPivot('statut', 'commentaire')
            ->withTimestamps();
    }

    /**
     * Relation avec le modèle Vehicle.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicule_id');
    }

    /**
     * Relation avec le modèle User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
