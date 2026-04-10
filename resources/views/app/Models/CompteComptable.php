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
        'intitule',
        'est_verrouille',
    ];

    protected $casts = [
        'est_verrouille' => 'boolean',
    ];
}
