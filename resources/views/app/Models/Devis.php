<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Devis extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'client_name',
        'contrat_ref',
        'issue_date',
        'due_date',
        'objet',
        'type_devis', // 'vente', 'location', 'service'
        'montant_ht',
        'tva',
        'total_ttc',
        'notes',
        'statut',
        
        // Champs spécifiques location
        'location_duree_jours',
        'location_tarif_journalier_ht',
        'location_tarif_journalier_ttc',
        'location_nombre_vehicules',
        'location_type_vehicules',
        'location_kilometrage_inclus',
        'location_frais_kilometrage_supplementaire',
        'location_caution',
        'location_assurance_incluse',
        'location_carburant_inclus',
        'location_chauffeur_inclus',
        
        // Dates de location
        'location_date_debut',
        'location_date_fin',
        'location_heure_debut',
        'location_heure_fin',
        
        // Coûts additionnels
        'frais_livraison_ht',
        'frais_livraison_ttc',
        'frais_mise_disposition_ht',
        'frais_mise_disposition_ttc',
        'frais_nettoyage_ht',
        'frais_nettoyage_ttc',
        
        // Validation et suivi
        'validated_by',
        'validated_at',
        'operation_id_associee',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'location_date_debut' => 'date',
        'location_date_fin' => 'date',
        'location_heure_debut' => 'datetime',
        'location_heure_fin' => 'datetime',
        'montant_ht' => 'decimal:2',
        'tva' => 'decimal:2',
        'total_ttc' => 'decimal:2',
        'location_tarif_journalier_ht' => 'decimal:2',
        'location_tarif_journalier_ttc' => 'decimal:2',
        'location_frais_kilometrage_supplementaire' => 'decimal:2',
        'location_caution' => 'decimal:2',
        'frais_livraison_ht' => 'decimal:2',
        'frais_livraison_ttc' => 'decimal:2',
        'frais_mise_disposition_ht' => 'decimal:2',
        'frais_mise_disposition_ttc' => 'decimal:2',
        'frais_nettoyage_ht' => 'decimal:2',
        'frais_nettoyage_ttc' => 'decimal:2',
        'location_assurance_incluse' => 'boolean',
        'location_carburant_inclus' => 'boolean',
        'location_chauffeur_inclus' => 'boolean',
        'validated_at' => 'datetime',
    ];

    /**
     * Relation avec le client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relation avec l'opération associée
     */
    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class, 'operation_id_associee');
    }

    /**
     * Relation avec le validateur
     */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /**
     * Scope pour les devis de location
     */
    public function scopeLocation($query)
    {
        return $query->where('type_devis', 'location');
    }

    /**
     * Scope pour les devis validés
     */
    public function scopeValidated($query)
    {
        return $query->whereNotNull('validated_at');
    }

    /**
     * Calculer le montant total de location
     */
    public function calculerMontantTotalLocation()
    {
        $montant_location = $this->location_tarif_journalier_ht * $this->location_duree_jours * $this->location_nombre_vehicules;
        
        $frais_additionnels = 
            ($this->frais_livraison_ht ?? 0) +
            ($this->frais_mise_disposition_ht ?? 0) +
            ($this->frais_nettoyage_ht ?? 0);
        
        $this->montant_ht = $montant_location + $frais_additionnels;
        $this->total_ttc = $this->montant_ht * (1 + ($this->tva / 100));
        
        return $this->total_ttc;
    }

    /**
     * Vérifier si le devis est une location
     */
    public function isLocation()
    {
        return $this->type_devis === 'location';
    }

    /**
     * Obtenir le nombre de jours de location
     */
    public function getNombreJoursLocation()
    {
        if ($this->location_date_debut && $this->location_date_fin) {
            return $this->location_date_debut->diffInDays($this->location_date_fin) + 1;
        }
        return $this->location_duree_jours ?? 0;
    }

    /**
     * Générer la référence du contrat de location
     */
    public static function generateReferenceContrat()
    {
        $year = date('Y');
        $month = date('m');
        $lastDevis = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastDevis ? intval(substr($lastDevis->contrat_ref ?? 'LOC-000', -3)) + 1 : 1;
        
        return "LOC-{$year}{$month}-" . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }
}
