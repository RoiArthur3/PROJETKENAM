<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Planification extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'titre',
        'description',
        'type_planification',
        'service_concerne_id',
        'date_planification',
        'heure_debut',
        'heure_fin',
        'lieu',
        'priorite',
        'statut',
        'createur_id',
        'participants',
        'documents',
        'notes',
        'budget_estime',
        'rapport_attendu',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_planification' => 'date',
        'heure_debut' => 'datetime:H:i',
        'heure_fin' => 'datetime:H:i',
        'participants' => 'array',
        'documents' => 'array',
        'budget_estime' => 'decimal:2',
        'rapport_attendu' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the service that owns the planification.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(ServiceOperationnel::class, 'service_concerne_id');
    }

    /**
     * Get the creator of the planification.
     */
    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'createur_id');
    }

    /**
     * Get the participants of the planification.
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'planification_participants')
                    ->withPivot('statut', 'date_confirmation')
                    ->withTimestamps();
    }

    /**
     * Get the type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type_planification) {
            'audit_interne' => 'Audit Interne',
            'audit_externe' => 'Audit Externe',
            'controle_qualite' => 'Contrôle Qualité',
            'inspection_securite' => 'Inspection Sécurité',
            'evaluation_risque' => 'Évaluation des Risques',
            'revue_processus' => 'Revue de Processus',
            'verification_conformite' => 'Vérification Conformité',
            default => $this->type_planification
        };
    }

    /**
     * Get the priority label.
     */
    public function getPrioriteLabelAttribute(): string
    {
        return match($this->priorite) {
            'basse' => 'Basse',
            'normale' => 'Normale',
            'haute' => 'Haute',
            'urgente' => 'Urgente',
            default => $this->priorite
        };
    }

    /**
     * Get the status label.
     */
    public function getStatutLabelAttribute(): string
    {
        return match($this->statut) {
            'planifie' => 'Planifiée',
            'en_cours' => 'En Cours',
            'terminee' => 'Terminée',
            'annulee' => 'Annulée',
            'reportee' => 'Reportée',
            default => $this->statut
        };
    }

    /**
     * Get the priority color.
     */
    public function getPrioriteColorAttribute(): string
    {
        return match($this->priorite) {
            'basse' => '#28a745',
            'normale' => '#007bff',
            'haute' => '#fd7e14',
            'urgente' => '#dc3545',
            default => '#6c757d'
        };
    }

    /**
     * Get the status color.
     */
    public function getStatutColorAttribute(): string
    {
        return match($this->statut) {
            'planifie' => '#007bff',
            'en_cours' => '#ffc107',
            'terminee' => '#28a745',
            'annulee' => '#dc3545',
            'reportee' => '#fd7e14',
            default => '#6c757d'
        };
    }

    /**
     * Get the type icon.
     */
    public function getTypeIconAttribute(): string
    {
        return match($this->type_planification) {
            'audit_interne' => 'fas fa-search',
            'audit_externe' => 'fas fa-external-link-alt',
            'controle_qualite' => 'fas fa-award',
            'inspection_securite' => 'fas fa-shield-alt',
            'evaluation_risque' => 'fas fa-exclamation-triangle',
            'revue_processus' => 'fas fa-cogs',
            'verification_conformite' => 'fas fa-check-double',
            default => 'fas fa-calendar-check'
        };
    }

    /**
     * Get the formatted date.
     */
    public function getDateFormateeAttribute(): string
    {
        return $this->date_planification->format('d/m/Y');
    }

    /**
     * Get the time range.
     */
    public function getPlageHoraireAttribute(): string
    {
        return $this->heure_debut . ' - ' . $this->heure_fin;
    }

    /**
     * Check if the planification is upcoming.
     */
    public function isUpcoming(): bool
    {
        return $this->date_planification->isFuture() ||
               ($this->date_planification->isToday() && now()->lt($this->heure_fin));
    }

    /**
     * Check if the planification is today.
     */
    public function isToday(): bool
    {
        return $this->date_planification->isToday();
    }

    /**
     * Check if the planification is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->date_planification->isPast() &&
               in_array($this->statut, ['planifie', 'en_cours']);
    }

    /**
     * Get the duration in hours.
     */
    public function getDureeAttribute(): float
    {
        $debut = \Carbon\Carbon::createFromFormat('H:i', $this->heure_debut);
        $fin = \Carbon\Carbon::createFromFormat('H:i', $this->heure_fin);

        return $debut->diffInHours($fin);
    }

    /**
     * Scope to get upcoming planifications.
     */
    public function scopeUpcoming($query)
    {
        return $query->whereDate('date_planification', '>=', today())
                    ->orderBy('date_planification', 'asc');
    }

    /**
     * Scope to get planifications by status.
     */
    public function scopeByStatut($query, string $statut)
    {
        return $query->where('statut', $statut);
    }

    /**
     * Scope to get planifications by priority.
     */
    public function scopeByPriorite($query, string $priorite)
    {
        return $query->where('priorite', $priorite);
    }

    /**
     * Scope to get planifications for a service.
     */
    public function scopeForService($query, int $serviceId)
    {
        return $query->where('service_concerne_id', $serviceId);
    }

    /**
     * Get the document URLs.
     */
    public function getDocumentUrlsAttribute(): array
    {
        if (empty($this->documents)) {
            return [];
        }

        return array_map(function ($document) {
            return asset('storage/' . $document);
        }, $this->documents);
    }

    /**
     * Get the participant count.
     */
    public function getParticipantCountAttribute(): int
    {
        return count($this->participants ?? []);
    }

    /**
     * Get the budget formatted.
     */
    public function getBudgetFormateAttribute(): string
    {
        if (!$this->budget_estime) {
            return 'Non spécifié';
        }

        return number_format($this->budget_estime, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Add a participant to the planification.
     */
    public function addParticipant(int $userId): void
    {
        $participants = $this->participants ?? [];
        if (!in_array($userId, $participants)) {
            $participants[] = $userId;
            $this->update(['participants' => $participants]);
        }
    }

    /**
     * Remove a participant from the planification.
     */
    public function removeParticipant(int $userId): void
    {
        $participants = $this->participants ?? [];
        $key = array_search($userId, $participants);
        if ($key !== false) {
            unset($participants[$key]);
            $this->update(['participants' => array_values($participants)]);
        }
    }

    /**
     * Get the planification statistics.
     */
    public function getStatistiques(): array
    {
        return [
            'jours_restants' => $this->date_planification->diffInDays(now()),
            'est_aujourdhui' => $this->isToday(),
            'est_a_venir' => $this->isUpcoming(),
            'est_en_retard' => $this->isOverdue(),
            'duree_heures' => $this->duree,
            'nombre_participants' => $this->participant_count,
            'nombre_documents' => count($this->documents ?? []),
        ];
    }
}
