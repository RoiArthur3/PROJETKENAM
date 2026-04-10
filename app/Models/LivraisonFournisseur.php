<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LivraisonFournisseur extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference',
        'fournisseur_id',
        'commande_id',
        'bon_livraison',
        'date_livraison',
        'date_reception',
        'transporteur',
        'numero_suivi',
        'frais_transport',
        'statut',
        'notes',
        'receptionnaire_id',
        'user_id',
    ];

    protected $casts = [
        'date_livraison' => 'date',
        'date_reception' => 'date',
        'frais_transport' => 'decimal:2',
    ];

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function commande()
    {
        return $this->belongsTo(CommandeFournisseur::class);
    }

    public function lignes()
    {
        return $this->belongsToMany(LigneCommandeFournisseur::class, 'ligne_livraison_fournisseur')
            ->withPivot(['quantite_livree', 'lot_serie', 'date_peremption', 'commentaire'])
            ->withTimestamps();
    }

    public function receptionnaire()
    {
        return $this->belongsTo(User::class, 'receptionnaire_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getEstReceptionneeAttribute()
    {
        return $this->statut === 'receptionnee';
    }

    public function getEstAnnuleeAttribute()
    {
        return $this->statut === 'annulee';
    }
}
