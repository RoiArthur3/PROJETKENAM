<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovisionnementDemande extends Model
{
    protected $table = 'approvisionnement_demandes';

    protected $fillable = [
        'numero_demande',
        'montant',
        'devise',
        'raison',
        'statut',
        'caisse_source_id',
        'caisse_destination_id',
        'demandeur_id',
        'approuve_par_id',
        'approvisionnement_caisse_id',
        'approved_at',
        'executed_at',
        'rejected_at',
        'rejection_reason',
    ];

    protected $casts = [
        'montant'     => 'decimal:2',
        'approved_at' => 'datetime',
        'executed_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (!$model->numero_demande) {
                $date  = now()->format('Ymd');
                $count = self::whereDate('created_at', today())->count() + 1;
                $model->numero_demande = sprintf('DAPP-%s-%05d', $date, $count);
            }
        });
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    public function demandeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'demandeur_id');
    }

    public function approuveur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approuve_par_id');
    }

    public function caisseSource(): BelongsTo
    {
        return $this->belongsTo(Caisse::class, 'caisse_source_id');
    }

    public function caisseDestination(): BelongsTo
    {
        return $this->belongsTo(Caisse::class, 'caisse_destination_id');
    }

    public function approvisionnementCaisse(): BelongsTo
    {
        return $this->belongsTo(ApprovisionnementCaisse::class, 'approvisionnement_caisse_id');
    }

    // ─── Helpers de statut ────────────────────────────────────────────────────

    public function isPending(): bool   { return $this->statut === 'pending'; }
    public function isPendingDg(): bool { return $this->statut === 'pending_dg'; }
    public function isApproved(): bool  { return $this->statut === 'approved'; }
    public function isRejected(): bool  { return $this->statut === 'rejected'; }
    public function isExecuted(): bool  { return $this->statut === 'executed'; }
    public function canExecute(): bool  { return $this->isApproved(); }

    public function statutLabel(): string
    {
        return match($this->statut) {
            'pending'  => 'En attente',
            'pending_dg' => 'En attente DG',
            'approved' => 'Approuvé',
            'rejected' => 'Refusé',
            'executed' => 'Exécuté',
            default    => ucfirst($this->statut),
        };
    }

    public function statutColor(): string
    {
        return match($this->statut) {
            'pending'  => 'warning',
            'pending_dg' => 'secondary',
            'approved' => 'info',
            'rejected' => 'danger',
            'executed' => 'success',
            default    => 'secondary',
        };
    }

    public function statutIcon(): string
    {
        return match($this->statut) {
            'pending'  => 'fas fa-clock',
            'pending_dg' => 'fas fa-user-tie',
            'approved' => 'fas fa-check',
            'rejected' => 'fas fa-times-circle',
            'executed' => 'fas fa-check-double',
            default    => 'fas fa-circle',
        };
    }
}
