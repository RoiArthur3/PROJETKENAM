<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Encaissement extends Model
{
    protected $fillable = [
        'invoice_id',
        'operation_id',
        'project_id',
        'client_id',
        'fournisseur_id',
        'reference',
        'reference_externe',
        'date_encaissement',
        'type_encaissement',
        'montant',
        'mode_paiement',
        'client',
        'caisse_id',
        'description',
        'notes',
        'piece_jointe',
        'statut',
        'created_by',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function operation()
    {
        return $this->belongsTo(Operation::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function clientRel()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    protected $casts = [
        'montant' => 'decimal:2',
        'date_encaissement' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($encaissement) {
            if (empty($encaissement->reference)) {
                $encaissement->reference = 'ENC-' . now()->format('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
            }
        });
    }

    public function caisse(): BelongsTo
    {
        return $this->belongsTo(Caisse::class);
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
