<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class InspectionItem extends Model
{
    use SoftDeletes;

    /**
     * Les statuts possibles d'un élément d'inspection.
     *
     * @var array<string, string>
     */
    public const STATUSES = [
        'pass' => 'Conforme',
        'fail' => 'Non conforme',
        'n/a' => 'Avec réserves',
    ];

    /**
     * Les types d'éléments possibles.
     *
     * @var array<string, string>
     */
    public const TYPES = [
        'boolean' => 'Oui/Non',
        'numeric' => 'Numérique',
        'text' => 'Texte',
        'select' => 'Sélection',
        'file' => 'Fichier',
    ];

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'inspection_id',
        'checklist_item_id',
        'item_name',
        'description',
        'type',
        'options',
        'value',
        'status',
        'notes',
        'attachments',
        'order',
        'is_critical',
        'completed_at',
    ];

    /**
     * Les attributs qui doivent être convertis.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'options' => 'array',
        'attachments' => 'array',
        'is_critical' => 'boolean',
        'completed_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Les valeurs par défaut des attributs du modèle.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_critical' => false,
    ];

    /**
     * Obtenir l'inspection à laquelle cet élément appartient.
     */
    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class, 'inspection_id');
    }

    /**
     * Obtenir l'élément de la checklist associé.
     */
    public function checklistItem(): BelongsTo
    {
        return $this->belongsTo(ChecklistItem::class, 'checklist_item_id');
    }

    /**
     * Obtenir les anomalies liées à cet élément d'inspection.
     */
    public function anomalies(): HasMany
    {
        return $this->hasMany(Anomaly::class, 'inspection_item_id');
    }

    /**
     * Marquer l'élément comme conforme.
     *
     * @param string|null $notes
     * @return bool
     */
    public function markAsPass(?string $notes = null): bool
    {
        return $this->update([
            'status' => 'pass',
            'notes' => $notes,
            'completed_at' => now(),
        ]);
    }

    /**
     * Marquer l'élément comme non conforme.
     *
     * @param string $notes
     * @return bool
     */
    public function markAsFail(string $notes): bool
    {
        return $this->update([
            'status' => 'fail',
            'notes' => $notes,
            'completed_at' => now(),
        ]);
    }

    /**
     * Marquer l'élément comme ayant des réserves.
     *
     * @param string $notes
     * @return bool
     */
    public function markAsWithReserves(string $notes): bool
    {
        return $this->update([
            'status' => 'n/a',
            'notes' => $notes,
            'completed_at' => now(),
        ]);
    }

    /**
     * Ajouter une pièce jointe à l'élément d'inspection.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $type
     * @param string|null $description
     * @return bool
     */
    public function addAttachment($file, string $type = 'image', ?string $description = null): bool
    {
        $attachments = $this->attachments ?? [];
        $filename = 'inspections/' . $this->inspection_id . '/' . uniqid() . '.' . $file->getClientOriginalExtension();
        
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
     * Supprimer une pièce jointe de l'élément d'inspection.
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
     * Obtenir le statut formaté pour l'affichage.
     *
     * @return string
     */
    public function getFormattedStatusAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
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
     * Obtenir la valeur formatée pour l'affichage.
     *
     * @return mixed
     */
    public function getFormattedValueAttribute()
    {
        if ($this->type === 'boolean') {
            return $this->value ? 'Oui' : 'Non';
        }

        if ($this->type === 'select' && !empty($this->options)) {
            return $this->options[$this->value] ?? $this->value;
        }

        return $this->value;
    }

    /**
     * Vérifier si l'élément est marqué comme conforme.
     *
     * @return bool
     */
    public function isPassed(): bool
    {
        return $this->status === 'pass';
    }

    /**
     * Vérifier si l'élément est marqué comme non conforme.
     *
     * @return bool
     */
    public function isFailed(): bool
    {
        return $this->status === 'fail';
    }

    /**
     * Vérifier si l'élément est marqué comme ayant des réserves.
     *
     * @return bool
     */
    public function hasReserves(): bool
    {
        return $this->status === 'n/a';
    }

    /**
     * Vérifier si l'élément est marqué comme critique.
     *
     * @return bool
     */
    public function isCritical(): bool
    {
        return (bool) $this->is_critical;
    }

    /**
     * Vérifier si l'élément nécessite un commentaire en cas d'échec.
     *
     * @return bool
     */
    public function requiresCommentOnFail(): bool
    {
        return $this->checklistItem->requires_comment_on_fail ?? true;
    }

    /**
     * Vérifier si l'élément nécessite une photo.
     *
     * @return bool
     */
    public function requiresPhoto(): bool
    {
        return $this->checklistItem->requires_photo ?? false;
    }

    /**
     * Scope pour les éléments conformes.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePassed($query)
    {
        return $query->where('status', 'pass');
    }

    /**
     * Scope pour les éléments non conformes.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'fail');
    }

    /**
     * Scope pour les éléments avec réserves.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithReserves($query)
    {
        return $query->where('status', 'n/a');
    }

    /**
     * Scope pour les éléments critiques.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCritical($query)
    {
        return $query->where('is_critical', true);
    }

    /**
     * Scope pour les éléments nécessitant une attention particulière.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNeedsAttention($query)
    {
        return $query->whereIn('status', ['fail', 'n/a']);
    }
}
