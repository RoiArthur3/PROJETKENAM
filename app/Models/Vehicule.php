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
        'date_debut_contrat',
        'provenance',
        'fournisseur_id'
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
            'type_materiel' => 'required|string|in:Engin,Machine,Camion',
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
            'provenance' => 'nullable|string|max:50',
            'fournisseur_id' => 'nullable|exists:fournisseurs,id',
        ];
    }

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    /**
     * Obtenir les opérations associées à ce véhicule
     */
    public function operations()
    {
        return $this->belongsToMany(Operation::class, 'operation_vehicule')
            ->withPivot(['date_affectation', 'date_fin_affectation', 'actif', 'notes'])
            ->withTimestamps()
            ->wherePivot('actif', true);
    }

    /**
     * Obtenir les affectations de ce véhicule
     */
    public function operationVehicules()
    {
        return $this->hasMany(OperationVehicule::class);
    }
}
