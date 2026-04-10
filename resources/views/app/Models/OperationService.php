<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationService extends Model
{
    use HasFactory;

    protected $fillable = [
        'operation_id',
        'service_name',
        'service_email',
        'destinataire_nom',
        'destinataire_email',
        'ordre',
        'statut',
        'valide_par',
        'valide_le',
        'commentaire',
    ];

    protected $casts = [
        'valide_le' => 'datetime',
    ];

    public function operation()
    {
        return $this->belongsTo(Operation::class);
    }
}
