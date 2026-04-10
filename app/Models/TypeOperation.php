<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeOperation extends Model
{
    use HasFactory;

    protected $table = 'types_operations';

    protected $fillable = [
        'code',
        'libelle',
        'description',
        'couleur',
        'icone',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    public function operations()
    {
        return $this->hasMany(Operation::class, 'type_operation_id');
    }
}
