<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';

    protected $fillable = [
        'code',
        'raison_sociale',
        'contact_nom',
        'contact_prenom',
        'email',
        'telephone',
        'adresse',
        'ville',
        'pays',
        'ice',
        'if',
        'patente',
        'cnss',
        'type',
        'statut',
        'notes',
        'entreprise',
        'description',
        'credit_limit',
        'actif',
        'nom',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'actif' => 'boolean',
    ];

    /**
     * Accesseur pour maintenir la compatibilité avec l'ancien code qui utilise "nom"
     * au lieu de "raison_sociale"
     *
     * @return string
     */
    public function getNomAttribute()
    {
        return $this->raison_sociale;
    }
}
