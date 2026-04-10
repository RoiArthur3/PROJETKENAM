<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacialRecognitionRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'personnel_id',
        'employee_code',
        'employee_name',
        'photo_base64',
        'face_id',
        'confidence_score',
        'recognition_time',
        'direction',
        'door_name',
        'event_type',
        'status',
        'sync_status',
        'sync_error',
        'synced_at',
        'camera_metadata'
    ];

    protected $casts = [
        'recognition_time' => 'datetime',
        'synced_at' => 'datetime',
        'confidence_score' => 'float',
        'camera_metadata' => 'array',
    ];

    /**
     * Relation avec le terminal facial
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(FacialDevice::class, 'device_id');
    }

    /**
     * Relation avec le personnel
     */
    public function personnel(): BelongsTo
    {
        return $this->belongsTo(Personnel::class, 'personnel_id');
    }

    /**
     * Scope pour les enregistrements réussis
     */
    public function scopeSuccessful($query)
    {
        return $query->where('confidence_score', '>=', 0.8)
                   ->where('status', 'recognized');
    }

    /**
     * Scope pour les enregistrements synchronisés
     */
    public function scopeSynced($query)
    {
        return $query->where('sync_status', 'synced');
    }

    /**
     * Scope pour les enregistrements en attente
     */
    public function scopePending($query)
    {
        return $query->where('sync_status', 'pending');
    }

    /**
     * Vérifier si l'enregistrement est valide
     */
    public function isValid(): bool
    {
        return $this->confidence_score >= 0.8 && 
               !empty($this->employee_code) && 
               !empty($this->photo_base64);
    }

    /**
     * Obtenir le statut formaté
     */
    public function getFormattedStatusAttribute(): string
    {
        return match($this->sync_status) {
            'pending' => 'En attente',
            'syncing' => 'Synchronisation...',
            'synced' => 'Synchronisé',
            'failed' => 'Échec',
            default => 'Inconnu'
        };
    }

    /**
     * Obtenir la direction formatée
     */
    public function getFormattedDirectionAttribute(): string
    {
        return match($this->direction) {
            'entry' => 'Entrée',
            'exit' => 'Sortie',
            default => $this->direction ?? 'Inconnue'
        };
    }
}
