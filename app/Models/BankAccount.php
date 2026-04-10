<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = [
        'nom_compte',
        'numero_compte',
        'banque',
        'solde_initial',
        'solde_actuel',
        'type',
        'actif',
        'description',
    ];

    protected $casts = [
        'solde_initial' => 'decimal:2',
        'solde_actuel' => 'decimal:2',
        'actif' => 'boolean',
    ];
}
