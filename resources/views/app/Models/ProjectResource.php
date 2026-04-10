<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectResource extends Model
{
    protected $fillable = [
        'project_id',
        'type',
        'resource_id',
        'quantite',
        'date_affectation',
        'date_liberation',
        'notes',
        'statut',
    ];

    protected $casts = [
        'date_affectation' => 'date',
        'date_liberation' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getResourceDetails()
    {
        switch ($this->type) {
            case 'vehicule':
                return Vehicule::find($this->resource_id);
            case 'chauffeur':
                return User::find($this->resource_id);
            case 'agent':
                return Agent::find($this->resource_id);
            case 'materiel':
                return Produit::find($this->resource_id);
            default:
                return null;
        }
    }
}
