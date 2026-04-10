<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CommandeFournisseur extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reference',
        'fournisseur_id',
        'contrat_id',
        'date_commande',
        'date_livraison_prevue',
        'date_livraison_reelle',
        'montant_ht',
        'tva',
        'montant_ttc',
        'statut',
        'mode_paiement',
        'conditions_paiement',
        'frais_livraison',
        'remise',
        'notes',
        'user_id'
    ];

    protected $casts = [
        'date_commande' => 'date',
        'date_livraison_prevue' => 'date',
        'date_livraison_reelle' => 'date',
        'montant_ht' => 'float',
        'tva' => 'float',
        'montant_ttc' => 'float',
        'frais_livraison' => 'float',
        'remise' => 'float',
        'notes' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($commande) {
            if (empty($commande->reference)) {
                $commande->reference = 'CMD-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }
            
            if (empty($commande->statut)) {
                $commande->statut = 'en_attente';
            }
            
            if (empty($commande->date_commande)) {
                $commande->date_commande = now();
            }
            
            if (empty($commande->tva)) {
                $commande->tva = 20.0; // TVA par défaut à 20%
            }
        });

        static::saved(function ($commande) {
            $commande->calculerMontants();
            
            // Mise à jour du fournisseur
            if ($commande->fournisseur) {
                $commande->fournisseur->touch();
            }
            
            // Mise à jour du contrat
            if ($commande->contrat) {
                $commande->contrat->touch();
            }
        });
    }

    public function calculerMontants()
    {
        $totalHT = $this->lignes->sum(function($ligne) {
            return $ligne->quantite * $ligne->prix_unitaire_ht * (1 - $ligne->remise / 100);
        });
        
        $this->montant_ht = $totalHT + $this->frais_livraison - $this->remise;
        $this->montant_ttc = $this->montant_ht * (1 + ($this->tva / 100));
        $this->saveQuietly();
    }

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function contrat()
    {
        return $this->belongsTo(ContratFournisseur::class, 'contrat_id');
    }

    public function lignes()
    {
        return $this->hasMany(LigneCommandeFournisseur::class, 'commande_id');
    }

    public function livraisons()
    {
        return $this->hasMany(LivraisonFournisseur::class, 'commande_id');
    }

    public function factures()
    {
        return $this->hasMany(FactureFournisseur::class, 'commande_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatutBadgeAttribute()
    {
        $badges = [
            'brouillon' => 'secondary',
            'en_attente' => 'warning',
            'validee' => 'info',
            'en_cours' => 'primary',
            'livree' => 'success',
            'partiellement_livree' => 'info',
            'annulee' => 'danger',
            'refusee' => 'danger'
        ];

        $statut = $this->statut;
        $libelle = ucfirst(str_replace('_', ' ', $statut));
        
        return sprintf('<span class="badge badge-%s">%s</span>', 
            $badges[$statut] ?? 'secondary', 
            $libelle
        );
    }

    public function getEstEnRetardAttribute()
    {
        if ($this->statut === 'annulee' || $this->statut === 'refusee' || $this->statut === 'livree') {
            return false;
        }
        
        return $this->date_livraison_prevue && Carbon::now()->gt(Carbon::parse($this->date_livraison_prevue));
    }

    public function getQuantiteCommandeeAttribute()
    {
        return $this->lignes->sum('quantite');
    }

    public function getQuantiteLivreeAttribute()
    {
        return $this->livraisons->sum(function($livraison) {
            return $livraison->lignes->sum('quantite_livree');
        });
    }

    public function getPourcentageLivreAttribute()
    {
        if ($this->quantite_commandee <= 0) return 0;
        return min(100, round(($this->quantite_livree / $this->quantite_commandee) * 100, 2));
    }

    public function getMontantPayeAttribute()
    {
        return $this->factures->sum('montant_paye');
    }

    public function getResteAPayerAttribute()
    {
        return max(0, $this->montant_ttc - $this->montant_paye);
    }

    public function getEstPayeeAttribute()
    {
        return $this->reste_a_payer <= 0;
    }
}
