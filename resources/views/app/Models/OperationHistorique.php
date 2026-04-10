<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationHistorique extends Model
{
    use HasFactory;

    protected $fillable = [
        'operation_id',
        'user_id',
        'action',
        'ancien_statut',
        'nouveau_statut',
        'commentaire',
        'donnees_modifiees',
    ];

    protected $casts = [
        'donnees_modifiees' => 'array',
        'created_at' => 'datetime',
    ];

    public const ACTIONS = [
        'CREATION' => 'Création',
        'ENVOI' => 'Envoi',
        'RECEPTION' => 'Réception',
        'COMMENTAIRE' => 'Commentaire',
        'CHANGEMENT_STATUT' => 'Changement de statut',
        'TRANSFERT' => 'Transfert',
        'CLOTURE' => 'Clôture',
        'AJOUT_PIECE_JOINTE' => 'Ajout de pièce jointe',
        'VALIDATION' => 'Validation',
    ];

    public const STATUTS = [
        'ENREGISTREE' => 'Enregistrée',
        'EN_ATTENTE_ENVOI' => 'En attente d\'envoi',
        'ENVOYEE' => 'Envoyée',
        'EN_COURS_DE_TRAITEMENT' => 'En cours de traitement',
        'TRANSFERE' => 'Transférée',
        'CLOTUREE' => 'Clôturée',
        'REJETEE' => 'Rejetée',
    ];

    // Relations
    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getActionLabelAttribute(): string
    {
        return self::ACTIONS[$this->action] ?? $this->action;
    }

    public function getStatutLabelAttribute(): string
    {
        return self::STATUTS[$this->nouveau_statut] ?? $this->nouveau_statut;
    }
}
