<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Inspection extends Model
{
    use SoftDeletes;

    /**
     * Les statuts possibles d'une inspection.
     *
     * @var array<string, string>
     */
    public const STATUSES = [
        'scheduled' => 'Planifiée',
        'in_progress' => 'En cours',
        'completed' => 'Terminée',
        'cancelled' => 'Annulée',
    ];

    /**
     * Les résultats possibles d'une inspection.
     *
     * @var array<string, string>
     */
    public const RESULTS = [
        'conform' => 'Conforme',
        'non_conform' => 'Non conforme',
        'conform_with_reserves' => 'Conforme avec réserves',
    ];

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reference',
        'checking_id',
        'checklist_id',
        'inspector_id',
        'started_at',
        'completed_at',
        'status',
        'result',
        'total_items',
        'passed_items',
        'failed_items',
        'items_with_reserves',
        'notes',
        'custom_fields',
        'signature_data',
        'signed_at',
        'created_by',
        'updated_by',
    ];

    /**
     * Les attributs qui doivent être convertis.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'signed_at' => 'datetime',
        'custom_fields' => 'array',
        'total_items' => 'integer',
        'passed_items' => 'integer',
        'failed_items' => 'integer',
        'items_with_reserves' => 'integer',
        'deleted_at' => 'datetime',
    ];

    /**
     * Les valeurs par défaut des attributs du modèle.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'scheduled',
        'total_items' => 0,
        'passed_items' => 0,
        'failed_items' => 0,
        'items_with_reserves' => 0,
    ];

    /**
     * Obtenir la vérification associée à cette inspection.
     */
    public function checking(): BelongsTo
    {
        return $this->belongsTo(Checking::class, 'checking_id');
    }

    /**
     * Obtenir la checklist utilisée pour cette inspection.
     */
    public function checklist(): BelongsTo
    {
        return $this->belongsTo(InspectionChecklist::class, 'checklist_id');
    }

    /**
     * Obtenir l'inspecteur qui a effectué l'inspection.
     */
    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    /**
     * Obtenir l'utilisateur qui a créé cette inspection.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Obtenir l'utilisateur qui a mis à jour cette inspection pour la dernière fois.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Obtenir les éléments inspectés lors de cette inspection.
     */
    public function items(): HasMany
    {
        return $this->hasMany(InspectionItem::class, 'inspection_id');
    }

    /**
     * Obtenir les anomalies détectées lors de cette inspection.
     */
    public function anomalies(): HasMany
    {
        return $this->hasMany(Anomaly::class, 'inspection_id');
    }

    /**
     * Obtenir le modèle inspecté (véhicule, équipement, etc.).
     */
    public function inspectable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Démarrer l'inspection.
     *
     * @return bool
     */
    public function start(): bool
    {
        if ($this->status !== 'scheduled') {
            return false;
        }

        return $this->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
    }

    /**
     * Terminer l'inspection.
     *
     * @param string $result
     * @param string|null $notes
     * @return bool
     */
    public function complete(string $result, ?string $notes = null): bool
    {
        if (!in_array($result, array_keys(self::RESULTS))) {
            return false;
        }

        // Calculer les statistiques
        $items = $this->items()->get();
        $totalItems = $items->count();
        $passedItems = $items->where('status', 'pass')->count();
        $failedItems = $items->where('status', 'fail')->count();
        $itemsWithReserves = $items->where('status', 'n/a')->count();

        return $this->update([
            'status' => 'completed',
            'result' => $result,
            'completed_at' => now(),
            'notes' => $notes,
            'total_items' => $totalItems,
            'passed_items' => $passedItems,
            'failed_items' => $failedItems,
            'items_with_reserves' => $itemsWithReserves,
        ]);
    }

    /**
     * Annuler l'inspection.
     *
     * @param string|null $reason
     * @return bool
     */
    public function cancel(?string $reason = null): bool
    {
        return $this->update([
            'status' => 'cancelled',
            'notes' => $reason ? ($this->notes ? $this->notes . "\n\nAnnulation: " . $reason : 'Annulation: ' . $reason) : $this->notes,
        ]);
    }

    /**
     * Ajouter une signature à l'inspection.
     *
     * @param string $signatureData
     * @param User $signedBy
     * @return bool
     */
    public function addSignature(string $signatureData, User $signedBy): bool
    {
        // Sauvegarder la signature (optionnel: stocker le fichier)
        $filename = 'signatures/inspection_' . $this->id . '_' . now()->format('YmdHis') . '.png';
        Storage::disk('public')->put($filename, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $signatureData)));

        return $this->update([
            'signature_data' => $filename,
            'signed_at' => now(),
            'updated_by' => $signedBy->id,
        ]);
    }

    /**
     * Vérifier si l'inspection est en retard.
     *
     * @return bool
     */
    public function isOverdue(): bool
    {
        return $this->checking->scheduled_at->isPast() && 
               !in_array($this->status, ['completed', 'cancelled']);
    }

    /**
     * Obtenir le statut formaté pour l'affichage.
     *
     * @return string
     */
    public function getFormattedStatusAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * Obtenir le résultat formaté pour l'affichage.
     *
     * @return string|null
     */
    public function getFormattedResultAttribute(): ?string
    {
        if (!$this->result) {
            return null;
        }

        return self::RESULTS[$this->result] ?? $this->result;
    }

    /**
     * Obtenir le pourcentage d'achèvement de l'inspection.
     *
     * @return int
     */
    public function getCompletionPercentageAttribute(): int
    {
        if ($this->total_items === 0) {
            return 0;
        }

        $completedItems = $this->items()->whereNotNull('completed_at')->count();
        return (int) round(($completedItems / $this->total_items) * 100);
    }

    /**
     * Obtenir le score d'inspection en pourcentage.
     *
     * @return int|null
     */
    public function getInspectionScoreAttribute(): ?int
    {
        if ($this->status !== 'completed' || $this->total_items === 0) {
            return null;
        }

        return (int) round(($this->passed_items / $this->total_items) * 100);
    }

    /**
     * Scope pour les inspections planifiées.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Scope pour les inspections en cours.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope pour les inspections terminées.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope pour les inspections en retard.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOverdue($query)
    {
        return $query->whereHas('checking', function ($q) {
            $q->where('scheduled_at', '<', now())
              ->whereNotIn('status', ['completed', 'cancelled']);
        });
    }
}
