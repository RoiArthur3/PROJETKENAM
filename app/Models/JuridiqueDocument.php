<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JuridiqueDocument extends Model
{
    use HasFactory;

    protected $table = 'juridique_documents';

    protected $fillable = [
        'contrat_id',
        'reference',
        'titre',
        'type_document',
        'chemin_fichier',
        'date_document',
        'date_expiration',
        'statut',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'date_document' => 'date',
        'date_expiration' => 'date',
    ];

    public function contrat(): BelongsTo
    {
        return $this->belongsTo(JuridiqueContrat::class, 'contrat_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope pour les documents expirant bientôt
     */
    public function scopeExpirantBientot($query, $jours = 30)
    {
        return $query->whereNotNull('date_expiration')
                    ->where('date_expiration', '<=', now()->addDays($jours))
                    ->where('date_expiration', '>=', now());
    }

    /**
     * Scope pour les documents expirés
     */
    public function scopeExpires($query)
    {
        return $query->whereNotNull('date_expiration')
                    ->where('date_expiration', '<', now());
    }
}
