<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Anomaly extends Model
{
    use SoftDeletes;

    /**
     * Les niveaux de gravité possibles d'une anomalie.
     *
     * @var array<string, string>
     */
    public const SEVERITIES = [
        'low' => 'Basse',
        'medium' => 'Moyenne',
        'high' => 'Haute',
        'critical' => 'Critique',
    ];

    /**
     * Les statuts possibles d'une anomalie.
     *
     * @var array<string, string>
     */
    public const STATUSES = [
        'open' => 'Ouverte',
        'in_progress' => 'En cours',
        'resolved' => 'Résolue',
        'rejected' => 'Rejetée',
        'closed' => 'Fermée',
    ];

    /**
     * Les sources possibles d'une anomalie.
     *
     * @var array<string, string>
     */
    public const SOURCES = [
        'inspection' => 'Inspection',
        'maintenance' => 'Maintenance',
        'user_report' => 'Signalement utilisateur',
        'system' => 'Système',
    ];

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reference',
        'inspection_id',
        'inspection_item_id',
        'title',
        'description',
        'severity',
        'status',
        'source',
        'assigned_to',
        'reported_by',
        'detected_at',
        'resolved_at',
        'target_resolution_date',
        'category',
        'subcategory',
        'tags',
        'impact',
        'urgency',
        'technical_data',
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
        'detected_at' => 'datetime',
        'resolved_at' => 'datetime',
        'target_resolution_date' => 'datetime',
        'tags' => 'array',
        'technical_data' => 'array',
        'attachments' => 'array',
        'impact' => 'integer',
        'urgency' => 'integer',
        'deleted_at' => 'datetime',
    ];

    /**
     * Les valeurs par défaut des attributs du modèle.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'severity' => 'medium',
        'status' => 'open',
        'source' => 'inspection',
        'impact' => 3,
        'urgency' => 3,
    ];

    /**
     * Obtenir l'inspection associée à cette anomalie.
     */
    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class, 'inspection_id');
    }

    /**
     * Obtenir l'élément d'inspection associé à cette anomalie.
     */
    public function inspectionItem(): BelongsTo
    {
        return $this->belongsTo(InspectionItem::class, 'inspection_item_id');
    }

    /**
     * Obtenir l'utilisateur à qui cette anomalie est assignée.
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Obtenir l'utilisateur qui a signalé cette anomalie.
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    /**
     * Obtenir l'utilisateur qui a créé cette anomalie.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Obtenir l'utilisateur qui a mis à jour cette anomalie pour la dernière fois.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Obtenir les actions correctives associées à cette anomalie.
     */
    public function correctiveActions(): HasMany
    {
        return $this->hasMany(CorrectiveAction::class, 'anomaly_id');
    }

    /**
     * Obtenir le modèle concerné par cette anomalie.
     */
    public function anomalizable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Assigner l'anomalie à un utilisateur.
     *
     * @param User $user
     * @return bool
     */
    public function assignTo(User $user): bool
    {
        return $this->update([
            'assigned_to' => $user->id,
            'status' => 'in_progress',
        ]);
    }

    /**
     * Marquer l'anomalie comme en cours de traitement.
     *
     * @return bool
     */
    public function markAsInProgress(): bool
    {
        return $this->update(['status' => 'in_progress']);
    }

    /**
     * Marquer l'anomalie comme résolue.
     *
     * @param string|null $notes
     * @return bool
     */
    public function markAsResolved(?string $notes = null): bool
    {
        return $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'notes' => $notes ? ($this->notes ? $this->notes . "\n\nRésolution: " . $notes : 'Résolution: ' . $notes) : $this->notes,
        ]);
    }

    /**
     * Rejeter l'anomalie.
     *
     * @param string $reason
     * @return bool
     */
    public function reject(string $reason): bool
    {
        return $this->update([
            'status' => 'rejected',
            'notes' => $this->notes ? $this->notes . "\n\nRejet: " . $reason : 'Rejet: ' . $reason,
        ]);
    }

    /**
     * Fermer l'anomalie.
     *
     * @param string|null $notes
     * @return bool
     */
    public function close(?string $notes = null): bool
    {
        return $this->update([
            'status' => 'closed',
            'notes' => $notes ? ($this->notes ? $this->notes . "\n\nFermeture: " . $notes : 'Fermeture: ' . $notes) : $this->notes,
        ]);
    }

    /**
     * Ajouter une pièce jointe à l'anomalie.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $type
     * @param string|null $description
     * @return bool
     */
    public function addAttachment($file, string $type = 'image', ?string $description = null): bool
    {
        $attachments = $this->attachments ?? [];
        $filename = 'anomalies/' . $this->id . '/' . uniqid() . '.' . $file->getClientOriginalExtension();
        
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
     * Supprimer une pièce jointe de l'anomalie.
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
     * Obtenir le niveau de gravité formaté pour l'affichage.
     *
     * @return string
     */
    public function getFormattedSeverityAttribute(): string
    {
        return self::SEVERITIES[$this->severity] ?? $this->severity;
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
     * Obtenir la source formatée pour l'affichage.
     *
     * @return string
     */
    public function getFormattedSourceAttribute(): string
    {
        return self::SOURCES[$this->source] ?? $this->source;
    }

    /**
     * Obtenir la priorité calculée (impact * urgence).
     *
     * @return int
     */
    public function getPriorityAttribute(): int
    {
        return $this->impact * $this->urgency;
    }

    /**
     * Obtenir le niveau de priorité formaté.
     *
     * @return string
     */
    public function getPriorityLevelAttribute(): string
    {
        $priority = $this->priority;

        if ($priority <= 4) {
            return 'Basse';
        } elseif ($priority <= 9) {
            return 'Moyenne';
        } elseif ($priority <= 16) {
            return 'Haute';
        } else {
            return 'Critique';
        }
    }

    /**
     * Vérifier si l'anomalie est en retard.
     *
     * @return bool
     */
    public function isOverdue(): bool
    {
        return $this->target_resolution_date && 
               $this->target_resolution_date->isPast() && 
               !in_array($this->status, ['resolved', 'rejected', 'closed']);
    }

    /**
     * Vérifier si l'anomalie est ouverte.
     *
     * @return bool
     */
    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    /**
     * Vérifier si l'anomalie est en cours de traitement.
     *
     * @return bool
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Vérifier si l'anomalie est résolue.
     *
     * @return bool
     */
    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    /**
     * Vérifier si l'anomalie est rejetée.
     *
     * @return bool
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Vérifier si l'anomalie est fermée.
     *
     * @return bool
     */
    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    /**
     * Scope pour les anomalies ouvertes.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope pour les anomalies en cours de traitement.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope pour les anomalies résolues.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    /**
     * Scope pour les anomalies rejetées.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope pour les anomalies fermées.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    /**
     * Scope pour les anomalies en retard.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOverdue($query)
    {
        return $query->where('target_resolution_date', '<', now())
                    ->whereNotIn('status', ['resolved', 'rejected', 'closed']);
    }

    /**
     * Scope pour les anomalies critiques.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCritical($query)
    {
        return $query->whereIn('severity', ['high', 'critical']);
    }

    /**
     * Scope pour les anomalies par gravité.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $severity
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfSeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }
}
