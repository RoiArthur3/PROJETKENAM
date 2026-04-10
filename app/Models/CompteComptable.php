<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompteComptable extends Model
{
    protected $table = 'comptes_comptables';

    protected $fillable = [
        'code',
        'libelle',
        'type',
        'numero',
        'numero_compte',
        'intitule',
        'description',
        'actif',
        'parent_id',
        'est_verrouille',
    ];

    protected $casts = [
        'actif' => 'boolean',
        'est_verrouille' => 'boolean',
    ];
}
