<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class PointageLogistique extends Model
{
    protected $table = 'pointages';

    protected $fillable = [
        'operation_id',
        'vehicle_id',
        'driver_id',
        'bon_commande_id',
        'date_pointage',
        'heure_debut',
        'heure_fin',
        'duree_heures',
        'duree_jours',
        'is_retard',
        'motif_retard',
        'kilometrage_debut',
        'kilometrage_fin',
        'kilometrage_jour',
        'carburant_debut',
        'carburant_fin',
        'carburant_consomme',
        'statut',
        'notes',
        'observations',
        'validated_by',
        'validated_at',
        'efficacite_score',
        'objectif_heures',
        'objectif_jours',
    ];

    protected $casts = [
        'date_pointage' => 'date',
        'heure_debut' => 'datetime:H:i',
        'heure_fin' => 'datetime:H:i',
        'duree_heures' => 'decimal:2',
        'duree_jours' => 'decimal:2',
        'is_retard' => 'boolean',
        'kilometrage_debut' => 'decimal:2',
        'kilometrage_fin' => 'decimal:2',
        'kilometrage_jour' => 'decimal:2',
        'carburant_debut' => 'decimal:2',
        'carburant_fin' => 'decimal:2',
        'carburant_consomme' => 'decimal:2',
        'validated_at' => 'datetime',
        'efficacite_score' => 'decimal:2',
        'objectif_heures' => 'decimal:2',
        'objectif_jours' => 'decimal:2',
    ];

    // Relations
    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicule::class, 'vehicle_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('date_pointage', today());
    }

    public function scopeByDriver($query, $driverId)
    {
        return $query->where('driver_id', $driverId);
    }

    public function scopeByVehicle($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    public function scopeByOperation($query, $operationId)
    {
        return $query->where('operation_id', $operationId);
    }

    public function scopeValidated($query)
    {
        return $query->where('statut', 'valide');
    }

    public function scopeEnRetard($query)
    {
        return $query->where('is_retard', true);
    }

    // Méthodes métier
    public function calculateDuree()
    {
        if ($this->heure_debut && $this->heure_fin) {
            $debut = Carbon::createFromFormat('H:i', $this->heure_debut);
            $fin = Carbon::createFromFormat('H:i', $this->heure_fin);

            // Si l'heure de fin est avant l'heure de début, c'est le lendemain
            if ($fin->lt($debut)) {
                $fin->addDay();
            }

            $this->duree_heures = $debut->diffInHours($fin) + ($debut->diffInMinutes($fin) % 60) / 60;
            $this->duree_jours = $this->duree_heures / 24; // 1 journée = 24h

            $this->save();
        }
    }

    public function checkRetard()
    {
        if ($this->operation && $this->heure_debut) {
            $operationStart = $this->operation->created_at;
            $pointageStart = Carbon::createFromFormat('H:i', $this->heure_debut);

            // Vérifier si plus de 48h entre début opération et premier pointage
            $diff = $operationStart->diffInHours($pointageStart);

            $this->is_retard = $diff > 48;
            $this->save();
        }
    }

    public function calculateKilometrage()
    {
        if ($this->kilometrage_debut && $this->kilometrage_fin) {
            $this->kilometrage_jour = $this->kilometrage_fin - $this->kilometrage_debut;
            $this->save();
        }
    }

    public function calculateCarburant()
    {
        if ($this->carburant_debut && $this->carburant_fin) {
            $this->carburant_consomme = $this->carburant_debut - $this->carburant_fin;
            $this->save();
        }
    }

    public function calculateEfficacite()
    {
        if ($this->objectif_heures > 0 && $this->duree_heures > 0) {
            // Efficacité = (heures réelles / objectif heures) * 100
            $this->efficacite_score = min(100, ($this->duree_heures / $this->objectif_heures) * 100);
            $this->save();
        }
    }

    public function getTotalJoursTravailles()
    {
        return self::where('driver_id', $this->driver_id)
            ->where('operation_id', $this->operation_id)
            ->where('statut', 'valide')
            ->sum('duree_jours');
    }

    public function getTotalHeuresTravaillees()
    {
        return self::where('driver_id', $this->driver_id)
            ->where('operation_id', $this->operation_id)
            ->where('statut', 'valide')
            ->sum('duree_heures');
    }

    public function getProgression()
    {
        if ($this->objectif_jours > 0) {
            $totalJours = $this->getTotalJoursTravailles();
            return min(100, ($totalJours / $this->objectif_jours) * 100);
        }
        return 0;
    }

    // Méthodes statiques
    public static function getStatsByDriver($driverId, $operationId)
    {
        $pointages = self::where('driver_id', $driverId)
            ->where('operation_id', $operationId)
            ->where('statut', 'valide')
            ->get();

        return [
            'total_jours' => $pointages->sum('duree_jours'),
            'total_heures' => $pointages->sum('duree_heures'),
            'total_km' => $pointages->sum('kilometrage_jour'),
            'total_carburant' => $pointages->sum('carburant_consomme'),
            'efficacite_moyenne' => $pointages->avg('efficacite_score'),
            'nb_jours_pointes' => $pointages->count(),
            'nb_retards' => $pointages->where('is_retard', true)->count(),
        ];
    }

    public static function getStatsByOperation($operationId)
    {
        $pointages = self::where('operation_id', $operationId)
            ->where('statut', 'valide')
            ->get();

        return [
            'total_jours' => $pointages->sum('duree_jours'),
            'total_heures' => $pointages->sum('duree_heures'),
            'total_km' => $pointages->sum('kilometrage_jour'),
            'total_carburant' => $pointages->sum('carburant_consomme'),
            'nb_pointages' => $pointages->count(),
            'nb_vehicules' => $pointages->pluck('vehicle_id')->unique()->count(),
            'nb_conducteurs' => $pointages->pluck('driver_id')->unique()->count(),
        ];
    }
}
