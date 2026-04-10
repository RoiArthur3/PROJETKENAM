<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalComptable extends Model
{
    use HasFactory;

    protected $table = 'journal_comptables';

    protected $fillable = [
        'code',
        'libelle',
        'type',
        'description',
        'couleur',
        'icone',
        'actif',
        'systeme',
    ];

    protected $casts = [
        'actif' => 'boolean',
        'systeme' => 'boolean',
    ];

    public function ecritures(): HasMany
    {
        return $this->hasMany(EcritureComptable::class, 'journal_id');
    }
}