<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EcritureComptable extends Model
{
    use HasFactory;

    protected $table = 'ecritures_comptables';

    protected $fillable = [
        'journal_id',
        'date',
        'reference',
        'piece_comptable',
        'piece_jointe',
        'piece_jointe_nom',
        'libelle',
        'compte_debit',
        'compte_credit',
        'montant',
        'description',
        'source_type',
        'source_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date' => 'date',
        'montant' => 'float',
    ];

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

    public function journal(): BelongsTo
    {
        return $this->belongsTo(JournalComptable::class, 'journal_id');
    }
}
