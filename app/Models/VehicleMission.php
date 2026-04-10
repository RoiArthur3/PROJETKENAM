<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\VehiclePointage;
use App\Models\VehicleFinancialEntry;

class VehicleMission extends Model
{
    protected $table = 'vehicle_missions';

    protected $fillable = [
        'vehicle_id','user_id','personnel_id','reference','destination','start_at','end_at','objective','status','start_km','end_km','notes',
        'driver_id','supplier_id','client_id','duration_days','daily_supplier_price','daily_client_price','total_supplier_cost','total_client_amount',
        'invoice_reference','is_billed','billed_at','is_revenue_recorded','source_type','source_id','source_reference',
        'pointage_submodule','billing_mode'
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'start_km' => 'integer',
        'end_km' => 'integer',
        'duration_days' => 'integer',
        'daily_supplier_price' => 'decimal:2',
        'daily_client_price' => 'decimal:2',
        'total_supplier_cost' => 'decimal:2',
        'total_client_amount' => 'decimal:2',
        'gross_margin' => 'decimal:2',
        'source_id' => 'integer',
        'is_billed' => 'boolean',
        'billed_at' => 'datetime',
        'is_revenue_recorded' => 'boolean',
    ];

    public function resolvePointageProfile(): array
    {
        $submodule = in_array($this->pointage_submodule, ['engin', 'camion_plateau'], true)
            ? $this->pointage_submodule
            : null;
        $billingMode = in_array($this->billing_mode, ['standard', 'monthly', 'trip'], true)
            ? $this->billing_mode
            : null;

        if ($submodule === null) {
            $vehicleType = strtolower((string) optional($this->vehicle)->type_materiel);
            $submodule = str_contains($vehicleType, 'camion') ? 'camion_plateau' : 'engin';
        }

        if ($submodule === 'engin') {
            $billingMode = 'standard';
        } elseif (!in_array($billingMode, ['monthly', 'trip'], true)) {
            $billingMode = 'trip';

            if ($this->source_type === 'project' && !empty($this->source_id)) {
                $projectType = strtolower((string) optional(\App\Models\Project::find($this->source_id))->type);
                if ($projectType === 'location') {
                    $billingMode = 'monthly';
                }
            }
        }

        return [
            'submodule' => $submodule,
            'billing_mode' => $billingMode,
        ];
    }

    public function vehicle(): BelongsTo { return $this->belongsTo(Vehicule::class, 'vehicle_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function personnel(): BelongsTo { return $this->belongsTo(Personnel::class); }
    public function driver(): BelongsTo { return $this->belongsTo(User::class, 'driver_id'); }
    public function supplier(): BelongsTo { return $this->belongsTo(\App\Models\Fournisseur::class, 'supplier_id'); }
    public function client(): BelongsTo { return $this->belongsTo(\App\Models\Client::class, 'client_id'); }
    public function pointages(): HasMany { return $this->hasMany(VehiclePointage::class, 'vehicle_mission_id'); }
    public function financialEntries(): HasMany { return $this->hasMany(VehicleFinancialEntry::class, 'vehicle_mission_id'); }

    public function getPointageSupplierCostAttribute(): float
    {
        $pointedCost = (float) $this->pointages()->sum('total_supplier_cost');

        return $pointedCost > 0 ? $pointedCost : (float) $this->total_supplier_cost;
    }

    public function getPointageClientRevenueAttribute(): float
    {
        $pointedRevenue = (float) $this->pointages()->sum('total_client_amount');

        return $pointedRevenue > 0 ? $pointedRevenue : (float) $this->total_client_amount;
    }

    public function getActualAdditionalChargesAttribute(): float
    {
        return (float) $this->financialEntries()->where('type', 'expense')->sum('amount');
    }

    public function getRecognizedRevenueAttribute(): float
    {
        return (float) $this->financialEntries()->where('type', 'revenue')->sum('amount');
    }

    public function getActualWorkHoursAttribute(): float
    {
        return (float) $this->pointages()->where('unit_type', 'heure')->sum('quantity');
    }

    public function getActualWorkDaysAttribute(): float
    {
        return (float) $this->pointages()->where('unit_type', 'jour')->sum('quantity');
    }

    public function getActualSupplierCostAttribute(): float
    {
        return $this->pointage_supplier_cost + $this->actual_additional_charges;
    }

    public function getActualClientRevenueAttribute(): float
    {
        $recognizedRevenue = $this->recognized_revenue;
        if ($recognizedRevenue > 0) {
            return $recognizedRevenue;
        }

        return $this->pointage_client_revenue;
    }

    public function getActualGrossMarginAttribute(): float
    {
        return $this->actual_client_revenue - $this->actual_supplier_cost;
    }

    /**
     * Enregistrer automatiquement la recette (CA) quand la mission est complétée
     */
    public function recordRevenue()
    {
        // Si montant > 0 et pas encore enregistré en recette
        if ($this->total_client_amount > 0 && !$this->is_revenue_recorded) {
            try {
                \App\Models\Recette::updateOrCreate(
                    ['reference' => 'MISSION-' . $this->id],
                    [
                        'libelle' => 'Recette Mission Véhicule #' . $this->id . ' - ' . $this->reference . ' : ' . $this->destination,
                        'montant' => $this->total_client_amount,
                        'date_recette' => $this->billed_at ?? now(),
                        'categorie' => 'Mission Véhicule',
                        'description' => 'Montant facturé au client pour la mission. Véhicule: ' . ($this->vehicle->name ?? 'N/A'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                // Marquer comme enregistré
                $this->update([
                    'is_revenue_recorded' => true,
                ]);

                Log::info("Recette enregistrée pour mission #{$this->id} : {$this->total_client_amount} FCFA");
                return true;
            } catch (\Exception $e) {
                Log::error("Erreur enregistrement recette mission #{$this->id}: " . $e->getMessage());
                return false;
            }
        }
        return false;
    }
}
