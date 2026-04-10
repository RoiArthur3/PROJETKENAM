<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Virement extends Model
{
    use SoftDeletes;

    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reference',
        'compte_source_id',
        'compte_destination_id',
        'montant',
        'date_virement',
        'statut',
        'motif',
        'notes',
        'initie_par',
        'valide_par',
        'date_validation',
        'reference_operation',
        'frais',
        'devise',
        'taux_change',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_virement' => 'date',
        'date_validation' => 'datetime',
        'montant' => 'decimal:2',
        'frais' => 'decimal:2',
        'taux_change' => 'decimal:6',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Les valeurs par défaut des attributs du modèle.
     *
     * @var array
     */
    protected $attributes = [
        'statut' => 'en_attente',
        'devise' => 'XOF',
        'taux_change' => 1,
        'frais' => 0,
    ];

    /**
     * Obtenir le compte source du virement.
     */
    public function compteSource(): BelongsTo
    {
        return $this->belongsTo(CompteBancaire::class, 'compte_source_id');
    }

    /**
     * Obtenir le compte destination du virement.
     */
    public function compteDestination(): BelongsTo
    {
        return $this->belongsTo(CompteBancaire::class, 'compte_destination_id');
    }

    /**
     * Obtenir l'utilisateur qui a initié le virement.
     */
    public function initiateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initie_par');
    }

    /**
     * Obtenir l'utilisateur qui a validé le virement.
     */
    public function validateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    /**
     * Obtenir le libellé du statut du virement.
     */
    public function getLibelleStatutAttribute(): string
    {
        return [
            'en_attente' => 'En attente',
            'effectue' => 'Effectué',
            'annule' => 'Annulé',
            'echec' => 'Échec',
        ][$this->statut] ?? $this->statut;
    }

    /**
     * Obtenir la classe CSS pour le statut du virement.
     */
    public function getClasseStatutAttribute(): string
    {
        return [
            'en_attente' => 'warning',
            'effectue' => 'success',
            'annule' => 'secondary',
            'echec' => 'danger',
        ][$this->statut] ?? 'secondary';
    }

    /**
     * Obtenir le montant total (montant + frais).
     */
    public function getMontantTotalAttribute(): float
    {
        return (float) bcadd($this->montant, $this->frais, 2);
    }

    /**
     * Obtenir le montant dans la devise de destination.
     */
    public function getMontantConvertiAttribute(): float
    {
        return (float) bcmul($this->montant, $this->taux_change, 2);
    }

    /**
     * Vérifier si le virement peut être annulé.
     */
    public function getPeutEtreAnnuleAttribute(): bool
    {
        return in_array($this->statut, ['en_attente', 'effectue']);
    }

    /**
     * Boot du modèle.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($virement) {
            if (empty($virement->reference)) {
                $virement->reference = 'VIR-' . date('Ymd') . '-' . strtoupper(uniqid());
            }
            if (empty($virement->initie_par)) {
                $virement->initie_par = auth()->id();
            }
            if (empty($virement->date_virement)) {
                $virement->date_virement = now();
            }
        });
    }
}
