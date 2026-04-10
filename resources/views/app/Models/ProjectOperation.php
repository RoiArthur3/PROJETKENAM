<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectOperation extends Model
{
    protected $fillable = [
        'project_id',
        'operation_id',
        'montant_alloue',
        'montant_consomme',
        'notes',
    ];

    protected $casts = [
        'montant_alloue' => 'decimal:2',
        'montant_consomme' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

    public function getRatioCoutAttribute()
    {
        if ($this->montant_alloue > 0) {
            return ($this->montant_consomme / $this->montant_alloue) * 100;
        }
        return 0;
    }

    public function isOverBudget()
    {
        return $this->montant_consomme > $this->montant_alloue;
    }
}
