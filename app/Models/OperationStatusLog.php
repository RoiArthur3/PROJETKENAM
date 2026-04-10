<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'operation_id',
        'from_status',
        'to_status',
        'user_name',
        'user_id',
        'commentaire',
    ];

    public function operation()
    {
        return $this->belongsTo(Operation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
