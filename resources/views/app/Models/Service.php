<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\ServicePersonneRessource;

class Service extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'code',
        'email',
        'password',
        'phone',
        'responsable',
        'description',
        'logo',
        'signature_email',
        'actif',
        'responsable_id',
        'responsable_email',
        'responsable_telephone',
        'responsable_compte_auto',
        'responsable_compte_cree_le',
        'responsable_notes',
        'modules'
    ];

    /**
     * Les attributs qui doivent être transformés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'actif' => 'boolean',
        'responsable_compte_auto' => 'boolean',
        'responsable_compte_cree_le' => 'datetime',
    ];

    /**
     * Les contrôles associés à ce service.
     */
    public function controles(): BelongsToMany
    {
        return $this->belongsToMany(Controle::class)
            ->withPivot('statut', 'commentaire')
            ->withTimestamps();
    }

    /**
     * Les utilisateurs appartenant à ce service.
     */
    public function users(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class, 'service_id');
    }

    /**
     * Le responsable du service.
     */
    public function responsable(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    /**
     * Opérations émises par ce service.
     */
    public function operationsEmises(): HasMany
    {
        return $this->hasMany(Operation::class, 'service_emetteur_id');
    }

    /**
     * Opérations reçues par ce service.
     */
    public function operationsRecues(): HasMany
    {
        return $this->hasMany(Operation::class, 'service_destinataire_id');
    }

    /**
     * Scope pour ne récupérer que les services actifs.
     */
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Obtenir la signature email formatée.
     */
    public function getSignatureEmailFormattedAttribute(): string
    {
        return $this->signature_email ?? "Cordialement,\nService {$this->nom}\nKENAM Services";
    }

    /**
     * Obtenir les personnes ressources du service
     */
    public function personneRessources(): HasMany
    {
        return $this->hasMany(ServicePersonneRessource::class);
    }

    /**
     * Obtenir les utilisateurs personnes ressources du service
     */
    public function personneRessourceUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'service_personne_ressources')
            ->withPivot(['role', 'recevoir_emails'])
            ->withTimestamps();
    }

    /**
     * Obtenir les emails des personnes ressources qui reçoivent les emails
     */
    public function getEmailsPersonnesRessources(): array
    {
        return $this->personneRessources()
            ->where('recevoir_emails', true)
            ->with('user')
            ->get()
            ->map(function ($personne) {
                return $personne->user->email;
            })
            ->filter()
            ->toArray();
    }

    /**
     * Obtenir le chef de service
     */
    public function chefService()
    {
        return $this->personneRessources()
            ->where('role', 'chef_service')
            ->with('user')
            ->first()?->user;
    }
}
