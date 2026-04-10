<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contrat extends Model
{
    use HasFactory;

    protected $table = 'contrats';

    protected $fillable = [
        'fournisseur_nom',
        'fournisseur_contact',
        'objet',
        'numero_contrat',
        'montant_total',
        'date_debut',
        'date_fin',
        'avances_versees',
        'statut',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'montant_total' => 'decimal:2',
        'avances_versees' => 'decimal:2',
    ];
}
