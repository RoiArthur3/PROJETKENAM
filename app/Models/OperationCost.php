<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'operation_id',
        'type', // depense|facture
        'description',
        'montant',
        'fournisseur',
    ];

    protected $casts = [
        'montant' => 'float',
    ];

    public function operation()
    {
        return $this->belongsTo(Operation::class);
    }
}
