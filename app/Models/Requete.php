<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Requete extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference',
        'objet',
        'description',
        'statut',
        'service_emetteur_id',
        'service_destinataire_id',
        'demandeur_id',
        'destinataire_id',
        'personne_ressource_id',
        'module_source',
        'operation_id',
        'date_envoi',
        'date_cloture',
    ];

    protected $casts = [
        'date_envoi' => 'datetime',
        'date_cloture' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $appends = ['statut_label'];

    public const STATUTS = [
        'ENREGISTREE' => 'Enregistrée',
        'EN_ATTENTE_ENVOI' => 'En attente d\'envoi',
        'ENVOYEE' => 'Envoyée',
        'EN_COURS_DE_TRAITEMENT' => 'En cours de traitement',
        'TRANSFERE' => 'Transférée',
        'CLOTUREE' => 'Clôturée',
        'REJETEE' => 'Rejetée',
    ];

    public const MODULES = [
        'operations' => 'Opérations',
        'parc_auto' => 'Parc Auto',
        'rh' => 'Ressources Humaines',
        'comptabilite' => 'Comptabilité',
        'stock' => 'Stock',
        'maintenance' => 'Maintenance',
        'achat' => 'Achat',
    ];

    public function getStatutLabelAttribute(): string
    {
        return self::STATUTS[$this->statut] ?? $this->statut;
    }

    public function generateReference(): string
    {
        $prefix = 'REQ';
        $date = now()->format('Ymd');
        $count = self::whereDate('created_at', now())->count() + 1;
        
        return $prefix . $date . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    // Relations
    public function serviceEmetteur(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_emetteur_id');
    }

    public function serviceDestinataire(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_destinataire_id');
    }

    public function demandeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'demandeur_id');
    }

    public function destinataire(): BelongsTo
    {
        return $this->belongsTo(User::class, 'destinataire_id');
    }

    public function personneRessource(): BelongsTo
    {
        return $this->belongsTo(User::class, 'personne_ressource_id');
    }

    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

    public function piecesJointes(): HasMany
    {
        return $this->hasMany(PieceJointe::class);
    }

    public function historiques(): HasMany
    {
        return $this->hasMany(RequeteHistorique::class)->orderBy('created_at', 'desc');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    // Scopes
    public function scopeParStatut($query, string $statut)
    {
        return $query->where('statut', $statut);
    }

    public function scopeParService($query, int $serviceId)
    {
        return $query->where('service_destinataire_id', $serviceId);
    }

    public function scopeParDemandeur($query, int $userId)
    {
        return $query->where('demandeur_id', $userId);
    }

    public function scopeEnAttente($query)
    {
        return $query->whereIn('statut', ['ENREGISTREE', 'EN_ATTENTE_ENVOI', 'ENVOYEE']);
    }

    public function scopeEnCours($query)
    {
        return $query->whereIn('statut', ['EN_COURS_DE_TRAITEMENT', 'TRANSFERE']);
    }

    public function scopeCloturees($query)
    {
        return $query->whereIn('statut', ['CLOTUREE', 'REJETEE']);
    }

    // Méthodes utilitaires
    public function changerStatut(string $nouveauStatut, ?string $commentaire = null, ?User $user = null)
    {
        $ancienStatut = $this->statut;
        $this->statut = $nouveauStatut;
        
        if ($nouveauStatut === 'ENVOYEE') {
            $this->date_envoi = now();
        }
        
        if (in_array($nouveauStatut, ['CLOTUREE', 'REJETEE'])) {
            $this->date_cloture = now();
        }
        
        $this->save();

        // Enregistrer dans l'historique
        $this->historiques()->create([
            'user_id' => $user?->id ?? auth()->id(),
            'action' => 'CHANGEMENT_STATUT',
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => $nouveauStatut,
            'commentaire' => $commentaire,
        ]);

        // Déclencher l'événement
        event(new \App\Events\RequeteStatutChange($this, $ancienStatut, $nouveauStatut));
    }

    public function envoyer()
    {
        $this->changerStatut('ENVOYEE', 'Requête envoyée au service destinataire');
    }

    public function cloturer(?string $commentaire = null, ?User $user = null)
    {
        $this->changerStatut('CLOTUREE', $commentaire, $user);
    }
}
