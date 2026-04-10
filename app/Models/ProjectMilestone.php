<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectMilestone extends Model
{
    protected $fillable = [
        'project_id',
        'titre',
        'description',
        'date_planifiee',
        'date_reelle',
        'pourcentage_completion',
        'statut',
        'remarques',
    ];

    protected $casts = [
        'date_planifiee' => 'date',
        'date_reelle' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function isRetarded()
    {
        if ($this->date_reelle && $this->date_planifiee) {
            return $this->date_reelle->isAfter($this->date_planifiee);
        }
        return false;
    }

    public function getDaysRetardAttribute()
    {
        if ($this->date_reelle && $this->date_planifiee) {
            return $this->date_reelle->diffInDays($this->date_planifiee);
        }
        return 0;
    }
}
