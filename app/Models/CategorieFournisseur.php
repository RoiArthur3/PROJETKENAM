<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategorieFournisseur extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nom',
        'description',
        'couleur',
        'est_actif'
    ];

    protected $casts = [
        'est_actif' => 'boolean'
    ];

    public function fournisseurs()
    {
        return $this->hasMany(Fournisseur::class);
    }
}
