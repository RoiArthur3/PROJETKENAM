<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarburantAppoint extends Model
{
    use HasFactory;

    protected $table = 'carburant_appoints';

    protected $fillable = [
        'vehicule_id',
        'type',
        'date',
        'litres',
        'prix_unitaire',
        'montant',
        'station',
    ];

    protected $casts = [
        'date' => 'datetime',
        'litres' => 'decimal:2',
        'prix_unitaire' => 'decimal:2',
        'montant' => 'decimal:2',
    ];

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }
}
