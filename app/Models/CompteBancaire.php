<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class CompteBancaire extends Model
{

    /**
     * Les types de comptes bancaires disponibles.
     *
     * @var array
     */
    public const TYPES_COMPTE = [
        'courant' => 'Compte courant',
        'epargne' => 'Compte épargne',
        'titre' => 'Compte titre',
        'professionnel' => 'Compte professionnel',
        'joint' => 'Compte joint',
    ];

    /**
     * Les devises disponibles.
     *
     * @var array
     */
    public const DEVISE = [
        'XOF' => 'Franc CFA (XOF)',
        'EUR' => 'Euro (EUR)',
        'USD' => 'Dollar US (USD)',
        'XAF' => 'Franc CFA (XAF)',
        'XPF' => 'Franc Pacifique (XPF)',
    ];

    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'banque_id',
        'numero_compte',
        'intitule_compte',
        'type_compte',
        'devise',
        'solde',
        'solde_ouverture',
        'date_ouverture',
        'date_fermeture',
        'nom_titulaire',
        'adresse_titulaire',
        'telephone_titulaire',
        'email_titulaire',
        'nom_contact',
        'telephone_contact',
        'email_contact',
        'decouvert_autorise',
        'taux_interet',
        'informations_supplementaires',
        'est_actif',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'solde' => 'decimal:2',
        'solde_ouverture' => 'decimal:2',
        'date_ouverture' => 'date',
        'date_fermeture' => 'date',
        'decouvert_autorise' => 'decimal:2',
        'taux_interet' => 'decimal:2',
        'est_actif' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Obtenir la banque à laquelle appartient ce compte.
     */
    public function banque(): BelongsTo
    {
        return $this->belongsTo(Banque::class);
    }

    /**
     * Obtenir les opérations bancaires associées à ce compte.
     */
    public function operations(): HasMany
    {
        return $this->hasMany(OperationBancaire::class);
    }

    /**
     * Obtenir le libellé du type de compte.
     *
     * @return string
     */
    public function getTypeCompteLibelleAttribute(): string
    {
        return self::TYPES_COMPTE[$this->type_compte] ?? $this->type_compte;
    }

    /**
     * Obtenir le libellé de la devise.
     *
     * @return string
     */
    public function getDeviseLibelleAttribute(): string
    {
        return self::DEVISE[$this->devise] ?? $this->devise;
    }

    /**
     * Vérifier si le compte est actif.
     *
     * @return bool
     */
    public function estActif(): bool
    {
        return $this->est_actif && ($this->date_fermeture === null || $this->date_fermeture > now());
    }

    /**
     * Vérifier si le compte est à découvert.
     *
     * @return bool
     */
    public function estADecouvert(): bool
    {
        return $this->solde < 0;
    }

    /**
     * Calculer le solde disponible (solde + découvert autorisé).
     *
     * @return float
     */
    public function getSoldeDisponibleAttribute(): float
    {
        return (float) bcadd($this->solde, $this->decouvert_autorise, 2);
    }

    /**
     * Scope pour les comptes actifs.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActif($query)
    {
        return $query->where('est_actif', true)
                    ->where(function($q) {
                        $q->whereNull('date_fermeture')
                          ->orWhere('date_fermeture', '>', now());
                    });
    }

    /**
     * Scope pour les comptes d'une banque spécifique.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $banqueId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDeBanque($query, int $banqueId)
    {
        return $query->where('banque_id', $banqueId);
    }

    /**
     * Formater le numéro de compte pour l'affichage.
     *
     * @return string
     */
    public function getNumeroFormateAttribute(): string
    {
        // Exemple: 12345 67890 12345678901 89
        return wordwrap($this->numero_compte, 5, ' ', true);
    }

    /**
     * Obtenir la durée du compte en années et mois.
     *
     * @return string
     */
    public function getDureeAttribute(): string
    {
        $dateDebut = Carbon::parse($this->date_ouverture);
        $dateFin = $this->date_fermeture ? Carbon::parse($this->date_fermeture) : now();

        $annees = $dateDebut->diffInYears($dateFin);
        $mois = $dateDebut->diffInMonths($dateFin) % 12;

        $duree = [];
        if ($annees > 0) {
            $duree[] = $annees . ' ' . ($annees > 1 ? 'ans' : 'an');
        }
        if ($mois > 0) {
            $duree[] = $mois . ' mois';
        }

        return $duree ? implode(' et ', $duree) : 'Moins d\'un mois';
    }
}
