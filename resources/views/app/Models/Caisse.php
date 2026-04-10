<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Caisse extends Model
{
    protected $fillable = [
        'code',
        'nom',
        'libelle',
        'type',
        'solde_initial',
        'solde_actuel',
        'devise',
        'responsable_id',
        'description',
        'est_active'
    ];

    protected $casts = [
        'solde_initial' => 'decimal:2',
        'solde_actuel' => 'decimal:2',
        'est_active' => 'boolean',
    ];

    protected $appends = ['variation', 'caisse', 'date_maj'];

    /**
     * Accesseur : variation = solde_actuel - solde_initial
     */
    public function getVariationAttribute()
    {
        return ($this->solde_actuel ?? 0) - ($this->solde_initial ?? 0);
    }

    /**
     * Accesseur : nom de la caisse (nom ou libelle)
     */
    public function getCaisseAttribute()
    {
        return $this->nom ?? $this->libelle ?? 'Caisse #' . $this->id;
    }

    /**
     * Accesseur : date de dernière mise à jour
     */
    public function getDateMajAttribute()
    {
        return $this->updated_at ?? $this->created_at;
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function approvisionnementsSource(): HasMany
    {
        return $this->hasMany(ApprovisionnementCaisse::class, 'caisse_source_id');
    }

    public function approvisionnementsDestination(): HasMany
    {
        return $this->hasMany(ApprovisionnementCaisse::class, 'caisse_destination_id');
    }

    public function depenses(): HasMany
    {
        return $this->hasMany(DepenseCaisse::class);
    }

    public function mouvements(): HasMany
    {
        return $this->hasMany(MouvementCaisse::class);
    }
}
