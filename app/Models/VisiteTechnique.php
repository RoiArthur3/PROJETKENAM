<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisiteTechnique extends Model
{
    protected $table = 'visites_techniques';

    protected $fillable = [
        'vehicle_id',
        'date_visite',
        'date_expiration',
        'centre_visite',
        'numero_certificat',
        'cout',
        'statut',
        'commentaires',
        'piece_jointe'
    ];

    protected $casts = [
        'date_visite' => 'date',
        'date_expiration' => 'date',
        'cout' => 'decimal:2'
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
