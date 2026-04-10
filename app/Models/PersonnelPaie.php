<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PersonnelPaie extends Model
{
    use HasFactory;

    protected $table = 'personnel_paies';

    protected $fillable = [
        'personnel_id',
        'periode',
        'salaire_base',
        'primes',
        'deductions',
        'salaire_net',
        'statut',
        'date_paiement',
        'observations',
        'cree_par',
        'paye_par',
    ];

    protected $casts = [
        'periode' => 'date',
        'salaire_base' => 'decimal:2',
        'primes' => 'decimal:2',
        'deductions' => 'decimal:2',
        'salaire_net' => 'decimal:2',
        'date_paiement' => 'date',
    ];

    // Statuts possibles
    const STATUTS = [
        'EN_ATTENTE' => 'En attente de validation',
        'VALIDE' => 'Validé',
        'PAYE' => 'Payé',
    ];

    // Relations
    public function personnel()
    {
        return $this->belongsTo(Personnel::class);
    }

    public function createur()
    {
        return $this->belongsTo(User::class, 'cree_par');
    }

    public function payeur()
    {
        return $this->belongsTo(User::class, 'paye_par');
    }

    // Scopes
    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'EN_ATTENTE');
    }

    public function scopeValides($query)
    {
        return $query->where('statut', 'VALIDE');
    }

    public function scopePayes($query)
    {
        return $query->where('statut', 'PAYE');
    }

    public function scopeDePeriode($query, $periode)
    {
        return $query->whereYear('periode', $periode->year)
                    ->whereMonth('periode', $periode->month);
    }

    public function scopeDeAnnee($query, $annee)
    {
        return $query->whereYear('periode', $annee);
    }

    public function scopeEntreDates($query, $debut, $fin)
    {
        return $query->whereBetween('periode', [$debut, $fin]);
    }

    // Accessors & Mutators
    public function getStatutLibelleAttribute()
    {
        return self::STATUTS[$this->statut] ?? $this->statut;
    }

    public function getPeriodeFormateeAttribute()
    {
        return Carbon::parse($this->periode)->format('F Y');
    }

    public function getPeriodeMoisAnneeAttribute()
    {
        return Carbon::parse($this->periode)->format('m/Y');
    }

    public function getSalaireBaseFormatteAttribute()
    {
        return number_format($this->salaire_base, 0, ',', ' ') . ' FCFA';
    }

    public function getPrimesFormatteAttribute()
    {
        return number_format($this->primes, 0, ',', ' ') . ' FCFA';
    }

    public function getDeductionsFormatteAttribute()
    {
        return number_format($this->deductions, 0, ',', ' ') . ' FCFA';
    }

    public function getSalaireNetFormatteAttribute()
    {
        return number_format($this->salaire_net, 0, ',', ' ') . ' FCFA';
    }

    public function getSalaireBrutAttribute()
    {
        return $this->salaire_base + $this->primes;
    }

    public function getSalaireBrutFormatteAttribute()
    {
        return number_format($this->salaire_brut, 0, ',', ' ') . ' FCFA';
    }

    // Méthodes métier
    public function estEnAttente()
    {
        return $this->statut === 'EN_ATTENTE';
    }

    public function estValide()
    {
        return $this->statut === 'VALIDE';
    }

    public function estPaye()
    {
        return $this->statut === 'PAYE';
    }

    public function peutEtreValide()
    {
        return $this->estEnAttente();
    }

    public function peutEtrePaye()
    {
        return $this->estValide();
    }

    public function peutEtreModifie()
    {
        return $this->estEnAttente();
    }

    public function peutEtreSupprime()
    {
        return !$this->estPaye();
    }

    public function marquerCommePaye($userId = null)
    {
        $this->update([
            'statut' => 'PAYE',
            'date_paiement' => now(),
            'paye_par' => $userId ?? Auth::id(),
        ]);
    }

    public function marquerCommeValide($userId = null)
    {
        $this->update([
            'statut' => 'VALIDE',
        ]);
    }

    // Calculs spécifiques à la Côte d'Ivoire
    public function getCnpsSalarieAttribute()
    {
        // 8% pour le salarié (CNPS)
        return round($this->salaire_base * 0.08, 0);
    }

    public function getCnpsEmployeurAttribute()
    {
        // 12.5% pour l'employeur (CNPS)
        return round($this->salaire_base * 0.125, 0);
    }

    public function getImpotSurSalaireAttribute()
    {
        // Calcul simplifié de l'impôt sur le salaire (BIC)
        // En réalité, ce serait plus complexe avec les tranches
        $salaireImposable = $this->salaire_base - $this->cnps_salarie;

        if ($salaireImposable <= 60000) {
            return 0;
        } elseif ($salaireImposable <= 120000) {
            return round(($salaireImposable - 60000) * 0.10, 0);
        } elseif ($salaireImposable <= 180000) {
            return round(6000 + ($salaireImposable - 120000) * 0.15, 0);
        } elseif ($salaireImposable <= 240000) {
            return round(15000 + ($salaireImposable - 180000) * 0.20, 0);
        } else {
            return round(27000 + ($salaireImposable - 240000) * 0.25, 0);
        }
    }

    public function getTotalChargesSalarieAttribute()
    {
        return $this->deductions + $this->cnps_salarie + $this->impot_sur_salaire;
    }

    public function getTotalCotiseEmployeurAttribute()
    {
        return $this->salaire_brut + $this->cnps_employeur;
    }

    public function genererLibellePaie()
    {
        return "Paie {$this->periode_formatee} - {$this->personnel->nom} {$this->personnel->prenoms}";
    }

    // Vérifications
    public function existeDejaPourPeriode()
    {
        return PersonnelPaie::where('personnel_id', $this->personnel_id)
            ->where('periode', $this->periode)
            ->where('id', '!=', $this->id)
            ->exists();
    }

    public function estCoherent()
    {
        $calculNet = $this->salaire_base + $this->primes - $this->deductions;
        return abs($calculNet - $this->salaire_net) < 0.01; // Tolérance de 1 centime
    }

    // Validation rules
    public static function getValidationRules()
    {
        return [
            'periode' => 'required|date|before_or_equal:today',
            'salaire_base' => 'required|numeric|min:0',
            'primes' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'salaire_net' => 'nullable|numeric|min:0',
            'statut' => 'required|in:' . implode(',', array_keys(self::STATUTS)),
            'date_paiement' => 'nullable|date',
            'observations' => 'nullable|string|max:1000',
        ];
    }

    public static function getValidationMessages()
    {
        return [
            'periode.required' => 'La période est obligatoire',
            'periode.before_or_equal' => 'La période ne peut pas être dans le futur',
            'salaire_base.required' => 'Le salaire de base est obligatoire',
            'salaire_base.min' => 'Le salaire de base doit être positif',
            'primes.min' => 'Les primes doivent être positives',
            'deductions.min' => 'Les déductions doivent être positives',
            'salaire_net.min' => 'Le salaire net doit être positif',
            'statut.required' => 'Le statut est obligatoire',
            'statut.in' => 'Le statut sélectionné n\'est pas valide',
        ];
    }

    // Statistiques
    public static function getStatistiquesMensuelles($personnelId, $annee)
    {
        return self::where('personnel_id', $personnelId)
            ->whereYear('periode', $annee)
            ->orderBy('periode')
            ->get()
            ->map(function($paie) {
                return [
                    'periode' => $paie->periode_formatee,
                    'salaire_base' => $paie->salaire_base,
                    'primes' => $paie->primes,
                    'deductions' => $paie->deductions,
                    'salaire_net' => $paie->salaire_net,
                    'statut' => $paie->statut_libelle,
                ];
            });
    }
}
