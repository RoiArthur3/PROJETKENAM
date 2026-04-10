<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LigneCommandeFournisseur extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'commande_id',
        'article_id',
        'reference_article',
        'designation',
        'description',
        'quantite',
        'unite',
        'prix_unitaire_ht',
        'tva_taux',
        'remise',
        'montant_ht',
        'montant_tva',
        'montant_ttc',
        'date_livraison_souhaitee',
        'date_livraison_prevue',
        'date_livraison_reelle',
        'statut',
        'notes'
    ];

    protected $casts = [
        'quantite' => 'float',
        'prix_unitaire_ht' => 'float',
        'tva_taux' => 'float',
        'remise' => 'float',
        'montant_ht' => 'float',
        'montant_tva' => 'float',
        'montant_ttc' => 'float',
        'date_livraison_souhaitee' => 'date',
        'date_livraison_prevue' => 'date',
        'date_livraison_reelle' => 'date',
        'notes' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($ligne) {
            $ligne->calculerMontants();
        });

        static::saved(function ($ligne) {
            $ligne->commande->calculerMontants();
        });

        static::deleted(function ($ligne) {
            $ligne->commande->calculerMontants();
        });
    }

    public function calculerMontants()
    {
        $montantHT = $this->quantite * $this->prix_unitaire_ht * (1 - $this->remise / 100);
        $montantTVA = $montantHT * ($this->tva_taux / 100);
        
        $this->montant_ht = $montantHT;
        $this->montant_tva = $montantTVA;
        $this->montant_ttc = $montantHT + $montantTVA;
    }

    public function commande()
    {
        return $this->belongsTo(CommandeFournisseur::class, 'commande_id');
    }

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }

    public function livraisons()
    {
        return $this->belongsToMany(
            LivraisonFournisseur::class,
            'ligne_livraison_fournisseur',
            'ligne_commande_id',
            'livraison_id'
        )->withPivot('quantite_livree', 'statut', 'notes')
         ->withTimestamps();
    }

    public function getQuantiteRestanteAttribute()
    {
        $quantiteLivree = $this->livraisons->sum('pivot.quantite_livree');
        return max(0, $this->quantite - $quantiteLivree);
    }

    public function getEstLivreAttribute()
    {
        return $this->quantite_restante <= 0;
    }

    public function getStatutBadgeAttribute()
    {
        $badges = [
            'en_attente' => 'warning',
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
        if ($this->est_livre || $this->statut === 'annulee' || $this->statut === 'refusee') {
            return false;
        }
        
        return $this->date_livraison_prevue && now()->gt($this->date_livraison_prevue);
    }
}
