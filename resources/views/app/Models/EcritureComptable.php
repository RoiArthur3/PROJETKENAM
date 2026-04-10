<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EcritureComptable extends Model
{
    protected $fillable = ['type', 'montant', 'date', 'compte_id', 'libelle'];

    public function scopeWherePeriode($query, $periode)
    {
        switch($periode) {
            case 'mois':
                return $query->whereMonth('date', now()->month);
            case 'trimestre':
                return $query->whereBetween('date', [
                    now()->startOfQuarter(),
                    now()->endOfQuarter()
                ]);
            case 'annee':
                return $query->whereYear('date', now()->year);
            default:
                return $query;
        }
    }
}
