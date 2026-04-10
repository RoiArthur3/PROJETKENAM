<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Banque extends Model
{

    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'code_banque',
        'code_guichet',
        'adresse',
        'ville',
        'pays',
        'telephone',
        'email',
        'site_web',
        'description',
        'est_active',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'est_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Obtenir les comptes bancaires associés à cette banque.
     */
    public function comptes(): HasMany
    {
        return $this->hasMany(CompteBancaire::class);
    }

    /**
     * Obtenir le nombre de comptes actifs pour cette banque.
     *
     * @return int
     */
    public function getNombreComptesActifsAttribute(): int
    {
        return $this->comptes()->where('est_actif', true)->count();
    }

    /**
     * Obtenir le solde total des comptes de cette banque.
     *
     * @return float
     */
    public function getSoldeTotalAttribute(): float
    {
        return $this->comptes()->sum('solde');
    }

    /**
     * Scope pour les banques actives.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('est_active', true);
    }

    /**
     * Scope pour rechercher une banque par son nom ou son code.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where('nom', 'like', "%{$search}%")
                    ->orWhere('code_banque', 'like', "%{$search}%");
    }
}
