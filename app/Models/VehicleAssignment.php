<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'operation_id',
        'driver_id',
        'assigned_at',
        'returned_at',
        'mission',
        'destination',
        'kilometrage_depart',
        'kilometrage_retour',
        'status',
        'notes',
        'report',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    protected $appends = ['status_label'];

    /**
     * Relation avec le véhicule
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicule::class);
    }

    /**
     * Relation avec l'opération
     */
    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

    /**
     * Relation avec le chauffeur
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Scope pour les affectations actives
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['assigned', 'in_progress']);
    }

    /**
     * Scope pour les affectations terminées
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Obtenir le libellé du statut
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'assigned' => 'Assigné',
            'in_progress' => 'En cours',
            'completed' => 'Terminé',
            'cancelled' => 'Annulé',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Calculer la distance parcourue
     */
    public function getDistanceAttribute(): int
    {
        if ($this->kilometrage_retour && $this->kilometrage_depart) {
            return $this->kilometrage_retour - $this->kilometrage_depart;
        }
        return 0;
    }
}
