<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JuridiqueFinancement extends Model
{
    use HasFactory;

    protected $table = 'juridique_financements';

    protected $fillable = [
        'reference',
        'titre',
        'description',
        'contrat_id',
        'type_financement',
        'organisme_preteur',
        'montant_emprunte',
        'taux_interet',
        'duree_mois',
        'date_debut',
        'date_fin',
        'mensualite',
        'devise',
        'statut',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'montant_emprunte' => 'decimal:2',
        'taux_interet' => 'decimal:4',
        'duree_mois' => 'integer',
        'mensualite' => 'decimal:2',
        'date_debut' => 'date',
        'date_fin' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Obtenir le contrat associé
     */
    public function contrat()
    {
        return $this->belongsTo(JuridiqueContrat::class, 'contrat_id');
    }

    /**
     * Obtenir les offres bancaires associées
     */
    public function offresBancaires()
    {
        return $this->hasMany(JuridiqueOffreBancaire::class, 'financement_id');
    }

    /**
     * Obtenir l'échéancier associé
     */
    public function echeanciers()
    {
        return $this->hasMany(JuridiqueEcheancier::class, 'financement_id');
    }

    /**
     * Obtenir l'utilisateur qui a créé le financement
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Obtenir le statut formaté
     */
    public function getStatutFormattedAttribute()
    {
        return match($this->statut) {
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
            'suspendu' => 'Suspendu',
            'annule' => 'Annulé',
            default => ucfirst($this->statut ?? 'Inconnu'),
        };
    }

    /**
     * Obtenir le type de financement formaté
     */
    public function getTypeFinancementFormattedAttribute()
    {
        return match($this->type_financement) {
            'bancaire' => 'Bancaire',
            'propre' => 'Propre',
            'leasing' => 'Leasing',
            'credit_bail' => 'Crédit-bail',
            'subvention' => 'Subvention',
            default => ucfirst($this->type_financement ?? 'Non défini'),
        };
    }

    /**
     * Obtenir le montant formaté
     */
    public function getMontantEmprunteFormattedAttribute()
    {
        return number_format($this->montant_emprunte ?? 0, 0, ',', ' ') . ' ' . ($this->devise ?? 'XOF');
    }

    /**
     * Obtenir le taux d'intérêt formaté
     */
    public function getTauxInteretFormattedAttribute()
    {
        return number_format($this->taux_interet ?? 0, 2, ',', ' ') . '%';
    }

    /**
     * Obtenir la mensualité formatée
     */
    public function getMensualiteFormattedAttribute()
    {
        return number_format($this->mensualite ?? 0, 0, ',', ' ') . ' ' . ($this->devise ?? 'XOF');
    }

    /**
     * Obtenir la durée formatée
     */
    public function getDureeFormattedAttribute()
    {
        return $this->duree_mois . ' mois';
    }

    /**
     * Vérifier si le financement est en cours
     */
    public function estEnCours()
    {
        return $this->statut === 'en_cours';
    }

    /**
     * Vérifier si le financement est terminé
     */
    public function estTermine()
    {
        return $this->statut === 'termine';
    }

    /**
     * Obtenir le nombre d'échéances payées
     */
    public function getEcheancesPayeesAttribute()
    {
        return $this->echeanciers()->where('statut', 'paye')->count();
    }

    /**
     * Obtenir le nombre total d'échéances
     */
    public function getTotalEcheancesAttribute()
    {
        return $this->echeanciers()->count();
    }

    /**
     * Obtenir le pourcentage de remboursement
     */
    public function getPourcentageRemboursementAttribute()
    {
        $total = $this->total_echeances;
        if ($total === 0) {
            return 0;
        }
        return round(($this->echeances_payees / $total) * 100, 2);
    }

    /**
     * Scope pour les financements en cours
     */
    public function scopeEnCours($query)
    {
        return $query->where('statut', 'en_cours');
    }

    /**
     * Scope pour les financements terminés
     */
    public function scopeTermines($query)
    {
        return $query->where('statut', 'termine');
    }

    /**
     * Scope pour les financements par type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type_financement', $type);
    }

    /**
     * Scope pour les financements par organisme
     */
    public function scopeByOrganisme($query, $organisme)
    {
        return $query->where('organisme_preteur', 'like', "%{$organisme}%");
    }
}
