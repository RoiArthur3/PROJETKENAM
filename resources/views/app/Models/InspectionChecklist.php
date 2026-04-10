<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InspectionChecklist extends Model
{
    use SoftDeletes;

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'type',
        'is_active',
        'version',
        'created_by',
        'updated_by',
    ];

    /**
     * Les attributs qui doivent être convertis.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'version' => 'integer',
        'deleted_at' => 'datetime',
    ];

    /**
     * Les valeurs par défaut des attributs du modèle.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_active' => true,
        'version' => 1,
    ];

    /**
     * Obtenir les éléments de la checklist.
     */
    public function items(): HasMany
    {
        return $this->hasMany(ChecklistItem::class, 'checklist_id')
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('order');
    }

    /**
     * Obtenir tous les éléments de la checklist (y compris les sous-éléments).
     */
    public function allItems(): HasMany
    {
        return $this->hasMany(ChecklistItem::class, 'checklist_id')
            ->with('children')
            ->orderBy('order');
    }

    /**
     * Obtenir les vérifications utilisant cette checklist.
     */
    public function checkings(): HasMany
    {
        return $this->hasMany(Checking::class, 'checklist_id');
    }

    /**
     * Obtenir l'utilisateur qui a créé cette checklist.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Obtenir l'utilisateur qui a mis à jour cette checklist pour la dernière fois.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Activer la checklist.
     *
     * @return bool
     */
    public function activate(): bool
    {
        return $this->update(['is_active' => true]);
    }

    /**
     * Désactiver la checklist.
     *
     * @return bool
     */
    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * Créer une nouvelle version de la checklist.
     *
     * @return self
     */
    public function createNewVersion(): self
    {
        $newChecklist = $this->replicate();
        $newChecklist->version = $this->version + 1;
        $newChecklist->is_active = false;
        $newChecklist->push();

        // Dupliquer les éléments de la checklist
        foreach ($this->allItems as $item) {
            $newItem = $item->replicate();
            $newItem->checklist_id = $newChecklist->id;
            $newItem->push();

            // Dupliquer les sous-éléments si nécessaire
            if ($item->children->isNotEmpty()) {
                $this->duplicateChildren($item->children, $newItem);
            }
        }

        return $newChecklist;
    }

    /**
     * Dupliquer les éléments enfants de manière récursive.
     *
     * @param \Illuminate\Database\Eloquent\Collection $children
     * @param \App\Models\ChecklistItem $parentItem
     * @return void
     */
    protected function duplicateChildren($children, $parentItem): void
    {
        foreach ($children as $child) {
            $newChild = $child->replicate();
            $newChild->checklist_id = $parentItem->checklist_id;
            $newChild->parent_id = $parentItem->id;
            $newChild->push();

            if ($child->children->isNotEmpty()) {
                $this->duplicateChildren($child->children, $newChild);
            }
        }
    }

    /**
     * Obtenir le type formaté pour l'affichage.
     *
     * @return string
     */
    public function getFormattedTypeAttribute(): string
    {
        $types = [
            'vehicle' => 'Véhicule',
            'equipment' => 'Équipement',
            'part' => 'Pièce',
            'operation' => 'Opération',
            'supplier' => 'Fournisseur',
        ];

        return $types[$this->type] ?? $this->type;
    }
}
