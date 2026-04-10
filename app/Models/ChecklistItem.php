<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChecklistItem extends Model
{
    use SoftDeletes;

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'checklist_id',
        'parent_id',
        'name',
        'description',
        'type',
        'options',
        'unit',
        'default_value',
        'min_value',
        'max_value',
        'is_required',
        'order',
        'is_critical',
        'requires_comment_on_fail',
        'requires_photo',
        'created_by',
        'updated_by',
    ];

    /**
     * Les attributs qui doivent être convertis.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'is_critical' => 'boolean',
        'requires_comment_on_fail' => 'boolean',
        'requires_photo' => 'boolean',
        'order' => 'integer',
        'deleted_at' => 'datetime',
    ];

    /**
     * Les valeurs par défaut des attributs du modèle.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'type' => 'boolean',
        'is_required' => true,
        'is_critical' => false,
        'requires_comment_on_fail' => true,
        'requires_photo' => false,
        'order' => 0,
    ];

    /**
     * Obtenir la checklist à laquelle cet élément appartient.
     */
    public function checklist(): BelongsTo
    {
        return $this->belongsTo(InspectionChecklist::class, 'checklist_id');
    }

    /**
     * Obtenir l'élément parent.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ChecklistItem::class, 'parent_id');
    }

    /**
     * Obtenir les éléments enfants.
     */
    public function children(): HasMany
    {
        return $this->hasMany(ChecklistItem::class, 'parent_id')
            ->with('children')
            ->orderBy('order');
    }

    /**
     * Obtenir les réponses d'inspection pour cet élément.
     */
    public function inspectionItems(): HasMany
    {
        return $this->hasMany(InspectionItem::class, 'checklist_item_id');
    }

    /**
     * Obtenir l'utilisateur qui a créé cet élément.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Obtenir l'utilisateur qui a mis à jour cet élément pour la dernière fois.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Vérifier si l'élément est une section.
     *
     * @return bool
     */
    public function isSection(): bool
    {
        return $this->type === 'section';
    }

    /**
     * Vérifier si l'élément est un champ de saisie.
     *
     * @return bool
     */
    public function isInputField(): bool
    {
        return !$this->isSection();
    }

    /**
     * Obtenir le type formaté pour l'affichage.
     *
     * @return string
     */
    public function getFormattedTypeAttribute(): string
    {
        $types = [
            'section' => 'Section',
            'boolean' => 'Oui/Non',
            'numeric' => 'Numérique',
            'text' => 'Texte',
            'select' => 'Sélection',
            'file' => 'Fichier',
        ];

        return $types[$this->type] ?? $this->type;
    }

    /**
     * Obtenir les options formatées pour les champs de type select.
     *
     * @return array
     */
    public function getFormattedOptionsAttribute(): array
    {
        if ($this->type !== 'select' || empty($this->options)) {
            return [];
        }

        return $this->options;
    }

    /**
     * Vérifier si l'élément a des enfants.
     *
     * @return bool
     */
    public function hasChildren(): bool
    {
        return $this->children->isNotEmpty();
    }

    /**
     * Définir la valeur par défaut en fonction du type.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setDefaultValueAttribute($value): void
    {
        if ($this->type === 'boolean') {
            $this->attributes['default_value'] = (bool) $value;
        } elseif ($this->type === 'numeric') {
            $this->attributes['default_value'] = is_numeric($value) ? (float) $value : null;
        } else {
            $this->attributes['default_value'] = $value;
        }
    }

    /**
     * Obtenir la valeur par défaut formatée en fonction du type.
     *
     * @return mixed
     */
    public function getFormattedDefaultValueAttribute()
    {
        if ($this->default_value === null) {
            return null;
        }

        switch ($this->type) {
            case 'boolean':
                return (bool) $this->default_value;
            case 'numeric':
                return (float) $this->default_value;
            case 'select':
                return $this->options[$this->default_value] ?? $this->default_value;
            default:
                return $this->default_value;
        }
    }

    /**
     * Scope pour les éléments de premier niveau (sans parent).
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRootItems($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope pour les éléments actifs.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
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
}
