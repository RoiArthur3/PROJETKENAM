<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entretien extends Model
{
    use HasFactory;

    protected $table = 'entretiens';

    protected $fillable = [
        'vehicule_id',
        'date',
        'type',
        'kilometrage',
        'prestataire',
        'cout',
        'description',
        'statut',
        'user_id',
    ];

    protected $casts = [
        'date' => 'date',
        'kilometrage' => 'integer',
        'cout' => 'decimal:2',
    ];

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
