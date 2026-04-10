<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reparation extends Model
{
    use HasFactory;

    protected $table = 'reparations';

    protected $fillable = [
        'vehicule_id',
        'date',
        'type_panne',
        'urgence',
        'description',
        'garage',
        'cout_estime',
        'cout_reel',
        'statut',
        'date_debut',
        'date_fin',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'date' => 'date',
        'date_debut' => 'date',
        'date_fin' => 'date',
        'cout_estime' => 'decimal:2',
        'cout_reel' => 'decimal:2',
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
