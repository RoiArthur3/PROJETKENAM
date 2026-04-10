<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Fournisseur extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reference',
        'raison_sociale',
        'forme_juridique',
        'siret',
        'tva_intracom',
        'categorie_id',
        'adresse',
        'code_postal',
        'ville',
        'pays',
        'telephone',
        'email',
        'site_web',
        'contacts',
        'notes',
        'est_actif',
        'evaluation_moyenne',
        'user_id'
    ];

    protected $casts = [
        'contacts' => 'array',
        'notes' => 'array',
        'est_actif' => 'boolean',
        'evaluation_moyenne' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($fournisseur) {
            if (empty($fournisseur->reference)) {
                $fournisseur->reference = 'FOURN-' . strtoupper(Str::random(8));
            }
        });
    }

    public function categorie()
    {
        return $this->belongsTo(CategorieFournisseur::class, 'categorie_id');
    }

    public function contrats()
    {
        return $this->hasMany(ContratFournisseur::class);
    }

    public function commandes()
    {
        return $this->hasMany(CommandeFournisseur::class);
    }

    public function evaluations()
    {
        return $this->hasMany(EvaluationFournisseur::class);
    }

    public function documents()
    {
        return $this->hasMany(DocumentFournisseur::class);
    }

    public function factures()
    {
        return $this->hasMany(\App\Models\FactureFournisseur::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatutBadgeAttribute()
    {
        return $this->est_actif
            ? '<span class="badge badge-success">Actif</span>'
            : '<span class="badge badge-danger">Inactif</span>';
    }

    public function getPerformanceMoyenneAttribute()
    {
        if (!$this->evaluations->count()) {
            return 0;
        }

        return round($this->evaluations->avg('note_finale'), 1);
    }

    public function getPerformanceBadgeAttribute()
    {
        $note = $this->performance_moyenne;

        if ($note >= 4) return '<span class="badge badge-success">Excellent</span>';
        if ($note >= 3) return '<span class="badge badge-info">Bon</span>';
        if ($note >= 2) return '<span class="badge badge-warning">Moyen</span>';
        return '<span class="badge badge-danger">Insuffisant</span>';
    }
}
