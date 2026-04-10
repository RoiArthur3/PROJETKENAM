<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paie extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mois',
        'annee',
        'salaire_base',
        'heures_sup',
        'primes',
        'brut',
        'cnps_salariale',
        'cnps_patronale',
        'autres_retenues',
        'net_a_payer',
        'statut',
        'date_paiement',
        'commentaires',
    ];

    protected $casts = [
        'salaire_base'    => 'float',
        'heures_sup'      => 'float',
        'primes'          => 'float',
        'brut'            => 'float',
        'cnps_salariale'  => 'float',
        'cnps_patronale'  => 'float',
        'autres_retenues' => 'float',
        'net_a_payer'     => 'float',
        'date_paiement'   => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
