<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class CorrectiveAction extends Model
{
    use SoftDeletes;

    /**
     * Les types d'actions correctives possibles.
     *
     * @var array<string, string>
     */
    public const TYPES = [
        'repair' => 'Réparation',
        'replacement' => 'Remplacement',
        'adjustment' => 'Ajustement',
        'cleaning' => 'Nettoyage',
        'calibration' => 'Étalonnage',
        'training' => 'Formation',
        'process_update' => 'Mise à jour du processus',
        'other' => 'Autre',
    ];

    /**
     * Les statuts possibles d'une action corrective.
     *
     * @var array<string, string>
     */
    public const STATUSES = [
        'pending' => 'En attente',
        'approved' => 'Approuvée',
        'in_progress' => 'En cours',
        'completed' => 'Terminée',
        'validated' => 'Validée',
        'cancelled' => 'Annulée',
    ];

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reference',
        'anomaly_id',
        'inspection_id',
        'title',
        'description',
        'type',
        'status',
        'assigned_to',
        'approved_by',
        'scheduled_start_date',
        'scheduled_end_date',
        'started_at',
        'completed_at',
        'time_spent_minutes',
        'estimated_cost',
        'actual_cost',
        'used_parts',
        'actions_taken',
        'results',
        'is_effective',
        'validated_by',
        'validated_at',
        'validation_notes',
        'attachments',
        'created_by',
        'updated_by',
    ];

    /**
     * Les attributs qui doivent être convertis.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'scheduled_start_date' => 'datetime',
        'scheduled_end_date' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'validated_at' => 'datetime',
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'time_spent_minutes' => 'integer',
        'is_effective' => 'boolean',
        'used_parts' => 'array',
        'attachments' => 'array',
        'deleted_at' => 'datetime',
    ];

    /**
     * Les valeurs par défaut des attributs du modèle.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'pending',
        'is_effective' => true,
    ];

    /**
     * Obtenir l'anomalie associée à cette action corrective.
     */
    public function anomaly(): BelongsTo
    {
        return $this->belongsTo(Anomaly::class, 'anomaly_id');
    }

    /**
     * Obtenir l'inspection associée à cette action corrective.
     */
    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class, 'inspection_id');
    }

    /**
     * Obtenir l'utilisateur à qui cette action est assignée.
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Obtenir l'utilisateur qui a approuvé cette action.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Obtenir l'utilisateur qui a validé cette action.
     */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /**
     * Obtenir l'utilisateur qui a créé cette action.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Obtenir l'utilisateur qui a mis à jour cette action pour la dernière fois.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Obtenir le modèle concerné par cette action corrective.
     */
    public function actionable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Approuver l'action corrective.
     *
     * @param User $user
     * @param string|null $notes
     * @return bool
     */
    public function approve(User $user, ?string $notes = null): bool
    {
        return $this->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'notes' => $notes ? ($this->notes ? $this->notes . "\n\nApprobation: " . $notes : 'Approbation: ' . $notes) : $this->notes,
        ]);
    }

    /**
     * Démarrer l'action corrective.
     *
     * @param string|null $notes
     * @return bool
     */
    public function start(?string $notes = null): bool
    {
        return $this->update([
            'status' => 'in_progress',
            'started_at' => now(),
            'notes' => $notes ? ($this->notes ? $this->notes . "\n\nDébut: " . $notes : 'Début: ' . $notes) : $this->notes,
        ]);
    }

    /**
     * Compléter l'action corrective.
     *
     * @param string $actionsTaken
     * @param string $results
     * @param float $actualCost
     * @param array $usedParts
     * @param string|null $notes
     * @return bool
     */
    public function complete(
        string $actionsTaken,
        string $results,
        float $actualCost,
        array $usedParts = [],
        ?string $notes = null
    ): bool {
        return $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'actions_taken' => $actionsTaken,
            'results' => $results,
            'actual_cost' => $actualCost,
            'used_parts' => $usedParts,
            'notes' => $notes ? ($this->notes ? $this->notes . "\n\nComplétion: " . $notes : 'Complétion: ' . $notes) : $this->notes,
        ]);
    }

    /**
     * Valider l'action corrective.
     *
     * @param User $user
     * @param bool $isEffective
     * @param string $validationNotes
     * @return bool
     */
    public function validateAction(User $user, bool $isEffective, string $validationNotes): bool
    {
        return $this->update([
            'status' => 'validated',
            'is_effective' => $isEffective,
            'validated_by' => $user->id,
            'validated_at' => now(),
            'validation_notes' => $validationNotes,
        ]);
    }

    /**
     * Annuler l'action corrective.
     *
     * @param string $reason
     * @return bool
     */
    public function cancel(string $reason): bool
    {
        return $this->update([
            'status' => 'cancelled',
            'notes' => $this->notes ? $this->notes . "\n\nAnnulation: " . $reason : 'Annulation: ' . $reason,
        ]);
    }

    /**
     * Ajouter une pièce jointe à l'action corrective.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $type
     * @param string|null $description
     * @return bool
     */
    public function addAttachment($file, string $type = 'document', ?string $description = null): bool
    {
        $attachments = $this->attachments ?? [];
        $filename = 'actions-correctives/' . $this->id . '/' . uniqid() . '.' . $file->getClientOriginalExtension();
        
        // Stocker le fichier
        $path = Storage::disk('public')->put($filename, file_get_contents($file));
        
        if (!$path) {
            return false;
        }

        $attachments[] = [
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'type' => $type,
            'description' => $description,
            'uploaded_at' => now()->toDateTimeString(),
        ];

        return $this->update(['attachments' => $attachments]);
    }

    /**
     * Supprimer une pièce jointe de l'action corrective.
     *
     * @param int $index
     * @return bool
     */
    public function removeAttachment(int $index): bool
    {
        $attachments = $this->attachments ?? [];
        
        if (!isset($attachments[$index])) {
            return false;
        }

        // Supprimer le fichier physique
        Storage::disk('public')->delete($attachments[$index]['filename']);
        
        // Supprimer l'entrée du tableau
        array_splice($attachments, $index, 1);
        
        return $this->update(['attachments' => $attachments]);
    }

    /**
     * Obtenir le type formaté pour l'affichage.
     *
     * @return string
     */
    public function getFormattedTypeAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
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
     * Obtenir le coût total des pièces utilisées.
     *
     * @return float
     */
    public function getUsedPartsTotalAttribute(): float
    {
        if (empty($this->used_parts)) {
            return 0.0;
        }

        return array_reduce($this->used_parts, function ($carry, $part) {
            return $carry + ($part['quantity'] * $part['unit_price']);
        }, 0.0);
    }

    /**
     * Obtenir le coût total (coût réel + pièces utilisées).
     *
     * @return float
     */
    public function getTotalCostAttribute(): float
    {
        return (float) $this->actual_cost + $this->used_parts_total;
    }

    /**
     * Vérifier si l'action est en retard.
     *
     * @return bool
     */
    public function isOverdue(): bool
    {
        return $this->scheduled_end_date && 
               $this->scheduled_end_date->isPast() && 
               !in_array($this->status, ['completed', 'validated', 'cancelled']);
    }

    /**
     * Vérifier si l'action est en attente.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Vérifier si l'action est approuvée.
     *
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Vérifier si l'action est en cours.
     *
     * @return bool
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Vérifier si l'action est terminée.
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Vérifier si l'action est validée.
     *
     * @return bool
     */
    public function isValidated(): bool
    {
        return $this->status === 'validated';
    }

    /**
     * Vérifier si l'action est annulée.
     *
     * @return bool
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Scope pour les actions en attente.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope pour les actions approuvées.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope pour les actions en cours.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope pour les actions terminées.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope pour les actions validées.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeValidated($query)
    {
        return $query->where('status', 'validated');
    }

    /**
     * Scope pour les actions annulées.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope pour les actions en retard.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOverdue($query)
    {
        return $query->where('scheduled_end_date', '<', now())
                    ->whereNotIn('status', ['completed', 'validated', 'cancelled']);
    }

    /**
     * Scope pour les actions par type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
