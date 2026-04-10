<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleFinancialEntry extends Model
{
    protected $table = 'vehicle_financial_entries';

    protected $fillable = [
        'vehicle_mission_id',
        'operation_id',
        'vehicle_id',
        'type',
        'category',
        'transaction_date',
        'label',
        'amount',
        'source_module',
        'depense_caisse_id',
        'facture_id',
        'external_reference',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];


    public static function entryTypes(): array
    {
        return [
            'expense' => 'Charge',
            'revenue' => 'Chiffre d\'affaires',
        ];
    }

    public static function categories(): array
    {
        return [
            'fuel' => 'Carburant',
            'driver_salary' => 'Salaire chauffeur',
            'maintenance' => 'Maintenance engin',
            'transport' => 'Transport de l\'engin',
            'site_misc' => 'Frais divers chantier',
            'invoice' => 'Facture client',
            'other' => 'Autre',
        ];
    }

    public static function sourceModules(): array
    {
        return [
            'manual' => 'Saisie manuelle',
            'tresorerie_decaissement' => 'Trésorerie - Décaissement',
            'tresorerie_avance' => 'Trésorerie - Acompte',
            'facture' => 'Module Facture',
        ];
    }

    public function mission(): BelongsTo
    {
        return $this->belongsTo(VehicleMission::class, 'vehicle_mission_id');
    }

    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicule::class, 'vehicle_id');
    }

    public function decaissement(): BelongsTo
    {
        return $this->belongsTo(DepenseCaisse::class, 'depense_caisse_id');
    }

    public function facture(): BelongsTo
    {
        return $this->belongsTo(Facture::class, 'facture_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::categories()[$this->category] ?? $this->category;
    }

    public function getSourceLabelAttribute(): string
    {
        return self::sourceModules()[$this->source_module] ?? $this->source_module;
    }
}
