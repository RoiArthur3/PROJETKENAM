<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationServiceValidation extends Model
{
    use HasFactory;

    protected $table = 'operation_service_validation';

    protected $fillable = [
        'operation_id',
        'service_operationnel_id',
        'ordre_validation',
        'statut',
        'commentaire',
        'date_validation',
        'validateur_id',
        'signature_path'
    ];

    protected $casts = [
        'date_validation' => 'datetime',
        'ordre_validation' => 'integer',
    ];

    public function operation()
    {
        return $this->belongsTo(Operation::class);
    }

    public function serviceOperationnel()
    {
        return $this->belongsTo(ServiceOperationnel::class, 'service_operationnel_id');
    }

    public function validateur()
    {
        return $this->belongsTo(User::class, 'validateur_id');
    }
}
