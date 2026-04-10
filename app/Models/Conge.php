<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conge extends Model
{
    use HasFactory;

    protected $table = 'conges';

    protected $fillable = [
        'user_id',
        'personnel_id',
        'agent_nom',
        'type_conge',
        'date_debut',
        'date_fin',
        'nombre_jours',
        'motif',
        'statut',
        'date_demande',
        'valideur_id',
        'date_validation',
        'commentaire_valideur',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'date_demande' => 'date',
        'date_validation' => 'datetime',
        'nombre_jours' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function personnel()
    {
        return $this->belongsTo(Personnel::class);
    }

    public function valideur()
    {
        return $this->belongsTo(User::class, 'valideur_id');
    }
}
