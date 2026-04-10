<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Expense",
 *     title="Dépense",
 *     description="Modèle représentant une dépense dans le système",
 *     @OA\Xml(name="Expense"),
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="reference", type="string", example="DEP-20231018-ABC123"),
 *     @OA\Property(property="categorie", type="string", example="Fournitures de bureau"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Achat de fournitures de bureau pour le service comptabilité"),
 *     @OA\Property(property="montant", type="number", format="float", example=125.50),
 *     @OA\Property(property="date_depense", type="string", format="date", example="2025-10-18"),
 *     @OA\Property(property="user_id", type="integer", format="int64", example=1),
 *     @OA\Property(property="operation_id", type="integer", format="int64", nullable=true, example=1),
 *     @OA\Property(property="fournisseur", type="string", nullable=true, example="PAPETERIE CENTRALE"),
 *     @OA\Property(property="statut", type="string", enum={"en_attente", "approuvee", "rejetee"}, example="en_attente"),
 *     @OA\Property(property="justificatif", type="string", nullable=true, example="justificatifs/facture_12345.pdf"),
 *     @OA\Property(property="mode_paiement", type="string", enum={"espece", "cheque", "virement", "carte_bancaire"}, example="carte_bancaire"),
 *     @OA\Property(property="service_concerne", type="string", example="Comptabilité"),
 *     @OA\Property(property="budget_prevu", type="number", format="float", nullable=true, example=1000.00),
 *     @OA\Property(property="approuve_par", type="integer", format="int64", nullable=true, example=2),
 *     @OA\Property(property="date_approbation", type="string", format="date-time", nullable=true, example="2025-10-19T10:30:00+00:00"),
 *     @OA\Property(property="rejet_raison", type="string", nullable=true, example="Justificatif manquant"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-10-18T08:30:00+00:00"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-10-18T10:45:00+00:00"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, example=null),
 *     @OA\Property(
 *         property="user",
 *         ref="#/components/schemas/User",
 *         description="Utilisateur ayant créé la dépense"
 *     ),
 *     @OA\Property(
 *         property="operation",
 *         ref="#/components/schemas/Operation",
 *         description="Opération liée à la dépense"
 *     ),
 *     @OA\Property(
 *         property="approver",
 *         ref="#/components/schemas/User",
 *         description="Utilisateur ayant approuvé la dépense"
 *     )
 * )
 */
class Expense extends Model
{
    protected $fillable = [
        'reference',
        'intitule',
        'categorie',
        'description',
        'montant',
        'date_depense',
        'user_id',
        'operation_id',
        'requete_id',
        'fournisseur',
        'statut',
        'justificatif',
        'mode_paiement',
        'service_concerne',
        'budget_prevu',
        'approuve_par',
        'date_approbation',
        'date_paiement',
        'payee_par',
        'reference_paiement',
        'rejet_raison',
        'vehicule_id',
        'quantite',
        'prix_unitaire',
        'type',
        'station',
    ];

    protected $casts = [
        'date_depense' => 'date',
        'date_approbation' => 'datetime',
        'date_paiement' => 'datetime',
        'montant' => 'decimal:2',
        'budget_prevu' => 'decimal:2',
    ];

    /**
     * Relation avec l'utilisateur qui a enregistré
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec l'opération liée
     */
    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

    public function requete(): BelongsTo
    {
        return $this->belongsTo(Requete::class);
    }

    /**
     * Relation avec l'utilisateur qui a approuvé
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approuve_par');
    }

    public function payeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payee_par');
    }

    /**
     * Relation avec le véhicule (pour les dépenses liées aux véhicules)
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicule_id');
    }

    /**
     * Scope pour les dépenses en attente
     */
    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    /**
     * Scope pour les dépenses approuvées
     */
    public function scopeApprouvees($query)
    {
        return $query->where('statut', 'approuvee');
    }

    /**
     * Scope pour les dépenses par service
     */
    public function scopeParService($query, $service)
    {
        return $query->where('service_concerne', $service);
    }
}
