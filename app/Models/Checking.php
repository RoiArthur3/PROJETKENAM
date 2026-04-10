<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Checking extends Model
{
    use SoftDeletes;

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reference',
        'title',
        'description',
        'type',
        'checklist_id',
        'inspector_id',
        'scheduled_at',
        'completed_at',
        'status',
        'result',
        'notes',
        'created_by',
        'updated_by',
    ];

    /**
     * Les attributs qui doivent être convertis.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Les valeurs par défaut des attributs du modèle.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'draft',
    ];

    /**
     * Obtenir la checklist associée à cette vérification.
     */
    public function checklist(): BelongsTo
    {
        return $this->belongsTo(InspectionChecklist::class, 'checklist_id');
    }

    /**
     * Obtenir l'inspecteur assigné à cette vérification.
     */
    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    /**
     * Obtenir l'utilisateur qui a créé cette vérification.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Obtenir l'utilisateur qui a mis à jour cette vérification pour la dernière fois.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Obtenir les inspections liées à cette vérification.
     */
    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class);
    }

    /**
     * Obtenir le modèle vérifiable associé (véhicule, équipement, etc.).
     */
    public function checkable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Marquer la vérification comme terminée.
     *
     * @param string $result
     * @param string|null $notes
     * @return bool
     */
    public function complete(string $result, ?string $notes = null): bool
    {
        return $this->update([
            'status' => 'completed',
            'result' => $result,
            'completed_at' => now(),
            'notes' => $notes,
        ]);
    }

    /**
     * Vérifier si la vérification est en retard.
     *
     * @return bool
     */
    public function isOverdue(): bool
    {
        return $this->scheduled_at->isPast() && 
               !in_array($this->status, ['completed', 'cancelled']);
    }

    /**
     * Obtenir le statut formaté pour l'affichage.
     *
     * @return string
     */
    public function getFormattedStatusAttribute(): string
    {
        $statuses = [
            'draft' => 'Brouillon',
            'scheduled' => 'Planifié',
            'in_progress' => 'En cours',
            'completed' => 'Terminé',
            'cancelled' => 'Annulé',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Obtenir le résultat formaté pour l'affichage.
     *
     * @return string
     */
    public function getFormattedResultAttribute(): ?string
    {
        if (!$this->result) {
            return null;
        }

        $results = [
            'conform' => 'Conforme',
            'non_conform' => 'Non conforme',
            'conform_with_reserves' => 'Conforme avec réserves',
        ];

        return $results[$this->result] ?? $this->result;
    }
}
