<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JuridiqueContrat extends Model
{
    use HasFactory;

    protected $table = 'juridique_contrats';

    protected $fillable = [
        'reference',
        'titre',
        'type_contrat',
        'partie_contractante',
        'objet',
        'date_signature',
        'date_debut',
        'date_fin',
        'montant',
        'devise',
        'statut',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'date_signature' => 'date',
        'date_debut' => 'date',
        'date_fin' => 'date',
        'montant' => 'decimal:2',
    ];

    public function documents(): HasMany
    {
        return $this->hasMany(JuridiqueDocument::class, 'contrat_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relations avec d'autres modules
     */
    public function financementDossiers(): HasMany
    {
        return $this->hasMany(FinancementDossier::class, 'contrat_id');
    }

    /**
     * Scope pour les contrats actifs
     */
    public function scopeActifs($query)
    {
        return $query->where('statut', 'actif')
                    ->where(function($q) {
                        $q->whereNull('date_fin')
                          ->orWhere('date_fin', '>=', now());
                    });
    }

    /**
     * Scope pour les contrats expirant bientôt
     */
    public function scopeExpirantBientot($query, $jours = 30)
    {
        return $query->where('statut', 'actif')
                    ->where('date_fin', '<=', now()->addDays($jours))
                    ->where('date_fin', '>=', now());
    }
}
