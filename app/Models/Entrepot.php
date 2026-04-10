<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrepot extends Model
{
    use HasFactory;

    protected $table = 'entrepots';

    protected $fillable = [
        'code',
        'nom',
        'adresse',
        'responsable',
        'telephone',
        'capacite',
        'capacite_utilisee',
        'actif',
        'description',
        'ville',
        'pays',
        'email',
        'superficie',
    ];

    protected $casts = [
        'capacite' => 'decimal:2',
        'capacite_utilisee' => 'decimal:2',
        'actif' => 'boolean',
        'superficie' => 'decimal:2',
    ];

    protected $attributes = [
        'capacite_utilisee' => 0,
        'actif' => true,
    ];

    public function produits()
    {
        return $this->hasMany(Produit::class);
    }

    public function inventaires()
    {
        return $this->hasMany(Inventaire::class);
    }

    public function mouvements()
    {
        return $this->hasMany(MouvementStock::class);
    }

    public function transfertsSource()
    {
        return $this->hasMany(TransfertStock::class, 'entrepot_source_id');
    }

    public function transfertsDestination()
    {
        return $this->hasMany(TransfertStock::class, 'entrepot_destination_id');
    }

    // Accesseurs pour le calcul du taux d'occupation
    public function getTauxOccupationAttribute()
    {
        if ($this->capacite > 0) {
            return ($this->capacite_utilisee / $this->capacite) * 100;
        }
        return 0;
    }

    public function getCapaciteDisponibleAttribute()
    {
        return $this->capacite - $this->capacite_utilisee;
    }

    public function getStatutTextAttribute()
    {
        return $this->actif ? 'Actif' : 'Inactif';
    }

    public function getStatutColorAttribute()
    {
        return $this->actif ? 'success' : 'secondary';
    }

    // Scope pour les entrepôts actifs
    public function scopeActifs($query)
    {
        return $query->where('actif', true);
    }

    // Scope pour les entrepôts avec capacité disponible
    public function scopeAvecCapaciteDisponible($query)
    {
        return $query->whereRaw('capacite > capacite_utilisee');
    }

    // Scope pour les entrepôts en alerte (plus de 90% d'occupation)
    public function scopeEnAlerte($query)
    {
        return $query->whereRaw('(capacite_utilisee / capacite) > 0.9');
    }
}
