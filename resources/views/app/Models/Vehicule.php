<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicule extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_materiel',
        'marque',
        'modele',
        'immatriculation',
        'annee',
        'couleur',
        'kilometrage',
        'date_achat',
        'date_fin_assurance',
        'prix_achat',
        'carburant',
        'notes',
        'disponible',
        'prix_location',
        'date_debut_contrat'
    ];

    protected $dates = [
        'date_achat',
        'date_fin_assurance',
        'date_debut_contrat',
        'created_at',
        'updated_at'
    ];

    public static function rules()
    {
        return [
            'type_materiel' => 'required|string|in:Vehicule,Machine,Camion',
            'marque' => 'required|string|max:50',
            'modele' => 'required|string|max:50',
            'immatriculation' => 'required|string|max:20',
            'annee' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'couleur' => 'nullable|string|max:30',
            'kilometrage' => 'nullable|integer|min:0',
            'date_achat' => 'nullable|date',
            'prix_achat' => 'nullable|numeric|min:0',
            'carburant' => 'nullable|string|max:20',
            'date_fin_assurance' => 'nullable|date',
            'notes' => 'nullable|string',
            'disponible' => 'boolean',
            'prix_location' => 'nullable|numeric|min:0',
            'date_debut_contrat' => 'nullable|date',
        ];
    }
}
