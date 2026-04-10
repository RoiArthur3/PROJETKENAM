<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Client;

class CommercialCommande extends Model
{
    protected $table = 'commercial_commandes';

    protected $fillable = [
        'reference',
        'client_id',
        'type_engin',
        'quantite',
        'lieu',
        'date_debut',
        'date_fin',
        'budget',
        'commentaire',
        'email_service',
        'cc_emails',
        'statut',
        'sent_at',
        'responded_at',
        'validated_at',
        'created_by',
        'validated_by',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'budget' => 'decimal:2',
        'cc_emails' => 'string',
        'sent_at' => 'datetime',
        'responded_at' => 'datetime',
        'validated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $commande) {
            if (empty($commande->reference)) {
                $commande->reference = 'COM-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }

            if (empty($commande->statut)) {
                $commande->statut = 'brouillon';
            }
        });
    }

    public function reponse()
    {
        return $this->hasOne(CommercialCommandeReponse::class, 'commande_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
