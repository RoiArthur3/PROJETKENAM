<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationStaff extends Model
{
    use HasFactory;

    protected $fillable = [
        'operation_id',
        'user_id',
        'role',
        'assigned_at',
        'started_at',
        'completed_at',
        'hours_worked',
        'overtime_hours',
        'status',
        'notes',
        'performance_notes',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'hours_worked' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
    ];

    /**
     * Relation avec l'opération
     */
    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope pour le personnel actif
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['assigned', 'active']);
    }

    /**
     * Scope pour le personnel par rôle
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Obtenir le libellé du rôle
     */
    public function getRoleLabelAttribute(): string
    {
        $labels = [
            'chauffeur' => 'Chauffeur',
            'assistant' => 'Assistant',
            'controleur' => 'Contrôleur',
            'agent_logistique' => 'Agent Logistique',
            'superviseur' => 'Superviseur',
        ];

        return $labels[$this->role] ?? $this->role;
    }

    /**
     * Obtenir le libellé du statut
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'assigned' => 'Assigné',
            'active' => 'Actif',
            'completed' => 'Terminé',
            'absent' => 'Absent',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Calculer les heures totales
     */
    public function getTotalHoursAttribute(): float
    {
        return $this->hours_worked + $this->overtime_hours;
    }

    /**
     * Démarrer la mission
     */
    public function startMission(): void
    {
        $this->started_at = now();
        $this->status = 'active';
        $this->save();
    }

    /**
     * Terminer la mission
     */
    public function completeMission(float $hoursWorked = null, float $overtimeHours = null): void
    {
        $this->completed_at = now();
        $this->status = 'completed';
        
        if ($hoursWorked !== null) {
            $this->hours_worked = $hoursWorked;
        }
        
        if ($overtimeHours !== null) {
            $this->overtime_hours = $overtimeHours;
        }
        
        $this->save();
    }
}
