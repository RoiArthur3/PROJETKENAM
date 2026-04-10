<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opportunite extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'titre',
        'montant',
        'statut',
        'probabilite',
        'date_echeance',
        'description',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'probabilite' => 'integer',
        'date_echeance' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
