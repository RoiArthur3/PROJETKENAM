<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinancementOffreBancaire extends Model
{
    use HasFactory;

    protected $table = 'financement_offres_bancaires';

    protected $fillable = [
        'dossier_id',
        'reference',
        'banque',
        'montant_propose',
        'taux_interet',
        'duree_mois',
        'date_proposition',
        'date_acceptation',
        'statut',
        'conditions',
        'created_by',
    ];

    protected $casts = [
        'montant_propose' => 'decimal:2',
        'taux_interet' => 'decimal:2',
        'date_proposition' => 'date',
        'date_acceptation' => 'date',
    ];

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(FinancementDossier::class, 'dossier_id');
    }

    public function echeances(): HasMany
    {
        return $this->hasMany(FinancementEcheance::class, 'offre_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope pour les offres acceptées
     */
    public function scopeAcceptees($query)
    {
        return $query->where('statut', 'acceptee');
    }

    /**
     * Générer un échéancier automatiquement
     */
    public function genererEcheancier()
    {
        if ($this->statut !== 'acceptee' || $this->duree_mois <= 0) {
            return false;
        }

        $montantMensuel = $this->montant_propose / $this->duree_mois;
        $dateDebut = $this->date_acceptation ?? now();

        for ($i = 1; $i <= $this->duree_mois; $i++) {
            FinancementEcheance::create([
                'offre_id' => $this->id,
                'dossier_id' => $this->dossier_id,
                'numero_echeance' => $i,
                'montant' => $montantMensuel,
                'date_echeance' => $dateDebut->copy()->addMonths($i),
                'statut' => 'a_payer',
                'created_by' => $this->created_by,
            ]);
        }

        return true;
    }
}
