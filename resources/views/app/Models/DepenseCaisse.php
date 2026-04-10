<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DepenseCaisse extends Model
{
    protected $fillable = [
        'operation_id',
        'reference',
        'caisse_id',
        'approvisionnement_id',
        'type_depense_id',
        'libelle',
        'description',
        'montant',
        'devise',
        'date_depense',
        'mode_paiement',
        'numero_cheque',
        'banque',
        'statut',
        'createur_id',
        'valideur_id',
        'date_validation',
        'motif_rejet',
        'tiers_id',
        'tiers_type',
        'projet_id',
        'compte_comptable_id',
        'paiement_fournisseur_id',
        'facture_fournisseur_id',
        'expense_id',
        'pieces_jointes',
        'notes',
        'created_by',
        'beneficiaire_id'
    ];

    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

    public static function getModesPaiement(): array
    {
        return [
            'especes' => 'Espèces',
            'cheque' => 'Chèque',
            'virement' => 'Virement',
            'carte' => 'Carte',
            'autre' => 'Autre',
        ];
    }

    protected $casts = [
        'montant' => 'decimal:2',
        'date_depense' => 'date',
        'est_justifie' => 'boolean',
    ];

    public function approvisionnement(): BelongsTo
    {
        return $this->belongsTo(ApprovisionnementCaisse::class);
    }

    public function caisse(): BelongsTo
    {
        return $this->belongsTo(Caisse::class);
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function justificatifs(): HasMany
    {
        return $this->hasMany(Justificatif::class, 'depense_id');
    }

    public function compteComptable(): BelongsTo
    {
        return $this->belongsTo(CompteComptable::class, 'compte_comptable_id');
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    public function beneficiaire(): BelongsTo
    {
        return $this->belongsTo(User::class, 'beneficiaire_id');
    }

    public function paiementFournisseur(): BelongsTo
    {
        return $this->belongsTo(PaiementFournisseur::class, 'paiement_fournisseur_id');
    }

    public function factureFournisseur(): BelongsTo
    {
        return $this->belongsTo(FactureFournisseur::class, 'facture_fournisseur_id');
    }
}
