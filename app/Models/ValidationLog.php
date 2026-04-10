<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ValidationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'validation_id',
        'user_id',
        'action',
        'commentaire',
        'ancien_statut',
        'nouveau_statut',
    ];

    protected $casts = [
        'ancien_statut' => 'array',
        'nouveau_statut' => 'array',
    ];

    public function validation(): BelongsTo
    {
        return $this->belongsTo(Validation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
