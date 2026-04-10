<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MouvementCaisse extends Model
{
    protected $table = 'mouvements_caisse';

    const TYPE_APPROVISIONNEMENT = 'approvisionnement';
    const TYPE_DEPENSE = 'depense';
    const TYPE_REMBOURSEMENT = 'remboursement';
    const TYPE_REGULARISATION = 'regularisation';

    protected $fillable = [
        'caisse_id',
        'type_mouvement',
        'reference',
        'libelle',
        'montant',
        'reference_type',
        'reference_id',
        'source_type',
        'source_id',
        'caisse_destination_id',
        'transfert_id',
        'devise',
        'date_mouvement',
        'description',
        'created_by',
        'updated_by',
        'statut',
        'notes',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_mouvement' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function normalizePayload(array $payload, ?EloquentModel $source = null): array
    {
        if (Schema::hasColumn('mouvements_caisse', 'reference') && empty($payload['reference'])) {
            $payload['reference'] = 'MVT-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        }

        if (Schema::hasColumn('mouvements_caisse', 'date_mouvement') && empty($payload['date_mouvement'])) {
            $payload['date_mouvement'] = now()->toDateString();
        }

        if (Schema::hasColumn('mouvements_caisse', 'devise') && empty($payload['devise'])) {
            $payload['devise'] = 'XOF';
        }

        if ($source) {
            if (Schema::hasColumn('mouvements_caisse', 'reference_type')) {
                $payload['reference_type'] = get_class($source);
                $payload['reference_id'] = $source->getKey();
            }

            if (Schema::hasColumn('mouvements_caisse', 'source_type')) {
                $payload['source_type'] = get_class($source);
                $payload['source_id'] = $source->getKey();
            }
        }

        if (Schema::hasColumn('mouvements_caisse', 'libelle') && empty($payload['libelle'])) {
            $payload['libelle'] = 'Mouvement de caisse';
        }

        if (Schema::hasColumn('mouvements_caisse', 'statut') && empty($payload['statut'])) {
            $payload['statut'] = 'brouillon';
        }

        if (Schema::hasColumn('mouvements_caisse', 'libelle')) {
            $montant = (float) ($payload['montant'] ?? 0);
            if (in_array(($payload['type_mouvement'] ?? null), [
                self::TYPE_APPROVISIONNEMENT,
                self::TYPE_DEPENSE,
                self::TYPE_REMBOURSEMENT,
                self::TYPE_REGULARISATION,
            ], true)) {
                $payload['type_mouvement'] = $montant < 0 ? 'sortie' : 'entree';
                $payload['montant'] = abs($montant);
            }
        }

        foreach (array_keys($payload) as $key) {
            if (!Schema::hasColumn('mouvements_caisse', $key)) {
                unset($payload[$key]);
            }
        }

        return $payload;
    }

    public function caisse(): BelongsTo
    {
        return $this->belongsTo(Caisse::class);
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function getLibelleTypeAttribute(): string
    {
        return [
            self::TYPE_APPROVISIONNEMENT => 'Approvisionnement',
            self::TYPE_DEPENSE => 'Dépense',
            self::TYPE_REMBOURSEMENT => 'Remboursement',
            self::TYPE_REGULARISATION => 'Régularisation',
        ][$this->type_mouvement] ?? 'Inconnu';
    }

    public function getMontantFormateAttribute(): string
    {
        $signe = in_array($this->type_mouvement, [
            self::TYPE_APPROVISIONNEMENT,
            self::TYPE_REMBOURSEMENT
        ]) ? '+' : '-';

        return $signe . ' ' . number_format($this->montant, 0, ',', ' ') . ' FCFA';
    }
}
