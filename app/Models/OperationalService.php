<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationalService extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'nom',
        'description',
        'email',
        'validateur_email',
        'emails_cc',
        'actif',
        'ordre',
    ];

    protected $casts = [
        'actif' => 'boolean',
        'ordre' => 'integer',
    ];
}
