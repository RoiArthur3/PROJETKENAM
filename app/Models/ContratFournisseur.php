<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ContratFournisseur extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reference',
        'fournisseur_id',
        'type_contrat',
        'date_debut',
        'date_fin',
        'montant_ht',
        'tva',
        'montant_ttc',
        'fichier_contrat',
        'conditions_paiement',
        'renouvellement_auto',
        'preavis_resiliation',
        'statut',
        'notes',
        'user_id'
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'montant_ht' => 'float',
        'tva' => 'float',
        'montant_ttc' => 'float',
        'renouvellement_auto' => 'boolean',
        'notes' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($contrat) {
            if (empty($contrat->reference)) {
                $contrat->reference = 'CTR-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }
            
            if (empty($contrat->statut)) {
                $contrat->statut = 'en_cours';
            }
            
            if (empty($contrat->tva)) {
                $contrat->tva = 20.0; // TVA par défaut à 20%
            }
            
            $contrat->calculerMontants();
        });

        static::saving(function ($contrat) {
            $contrat->calculerMontants();
        });
    }

    public function calculerMontants()
    {
        $this->montant_ht = (float) $this->montant_ht;
        $this->tva = (float) $this->tva;
        $this->montant_ttc = $this->montant_ht * (1 + ($this->tva / 100));
    }

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function commandes()
    {
        return $this->hasMany(CommandeFournisseur::class, 'contrat_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatutBadgeAttribute()
    {
        $badges = [
            'en_attente' => 'warning',
            'en_cours' => 'primary',
            'termine' => 'success',
            'resilie' => 'danger',
            'expire' => 'secondary'
        ];

        $statut = $this->statut;
        $libelle = ucfirst(str_replace('_', ' ', $statut));
        
        return sprintf('<span class="badge badge-%s">%s</span>', 
            $badges[$statut] ?? 'secondary', 
            $libelle
        );
    }

    public function getJoursRestantsAttribute()
    {
        if ($this->date_fin) {
            return Carbon::now()->diffInDays(Carbon::parse($this->date_fin), false);
        }
        return null;
    }

    public function getEstBientotExpireAttribute()
    {
        if (!$this->date_fin) return false;
        
        $joursRestants = $this->jours_restants;
        return $joursRestants > 0 && $joursRestants <= 30;
    }

    public function getEstExpireAttribute()
    {
        if (!$this->date_fin) return false;
        return Carbon::now()->gt(Carbon::parse($this->date_fin));
    }

    public function getMontantRestantAttribute()
    {
        $montantUtilise = $this->commandes()->sum('montant_ttc');
        return max(0, $this->montant_ttc - $montantUtilise);
    }

    public function getPourcentageUtiliseAttribute()
    {
        if ($this->montant_ttc <= 0) return 0;
        return min(100, round(($this->montant_ttc - $this->montant_restant) / $this->montant_ttc * 100, 2));
    }
}
