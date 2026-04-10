<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancementDossier extends Model
{
    use HasFactory;

    protected $table = 'financement_dossiers';

    protected $fillable = [
        'reference',
        'intitule',
        'type_financement',
        'organisme_cible',
        'montant_demande',
        'montant_obtenu',
        'devise',
        'date_depot',
        'date_validation',
        'statut',
        'description',
        'created_by',
        'contrat_id',
    ];

    protected $casts = [
        'montant_demande' => 'decimal:2',
        'montant_obtenu' => 'decimal:2',
        'date_depot' => 'date',
        'date_validation' => 'date',
    ];

    public function offresBancaires(): HasMany
    {
        return $this->hasMany(FinancementOffreBancaire::class, 'dossier_id');
    }

    public function echeances(): HasMany
    {
        return $this->hasMany(FinancementEcheance::class, 'dossier_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function contrat(): BelongsTo
    {
        return $this->belongsTo(JuridiqueContrat::class, 'contrat_id');
    }

    /**
     * Scope pour les dossiers en cours
     */
    public function scopeEnCours($query)
    {
        return $query->whereIn('statut', ['en_preparation', 'depose', 'en_etude', 'accepte']);
    }

    /**
     * Scope pour les dossiers terminés
     */
    public function scopeTermines($query)
    {
        return $query->whereIn('statut', ['refuse', 'termine']);
    }

    /**
     * Calculer le taux de succès
     */
    public function getTauxSuccesAttribute()
    {
        return $this->montant_demande > 0
            ? ($this->montant_obtenu / $this->montant_demande) * 100
            : 0;
    }
}
