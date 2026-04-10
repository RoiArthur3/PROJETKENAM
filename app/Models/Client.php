<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Services\ClientAccountService;

class Client extends Model
{
    protected $table = 'clients';

    protected $fillable = [
        'user_id',
        'code',
        'code_client',
        'client_code',
        'raison_sociale',
        'nom_complet',
        'company_name',
        'contact_nom',
        'contact_person',
        'contact_prenom',
        'email',
        'telephone',
        'phone',
        'adresse',
        'address',
        'ville',
        'city',
        'pays',
        'country',
        'ice',
        'if',
        'patente',
        'cnss',
        'type',
        'type_client',
        'client_type',
        'statut',
        'notes',
        'entreprise',
        'description',
        'credit_limit',
        'plafond_credit',
        'current_balance',
        'solde_du',
        'actif',
        'is_active',
        'est_actif',
        'nom',
        'assigned_agent_id',
        'registration_date',
        'payment_terms',
        'compte_comptable_id',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'plafond_credit' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'solde_du' => 'decimal:2',
        'actif' => 'boolean',
        'is_active' => 'boolean',
        'est_actif' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::created(function (Client $client) {
            try {
                app(ClientAccountService::class)->ensureForClient($client);
            } catch (\Throwable $e) {
                // noop: la creation client ne doit pas echouer si la couche comptable n'est pas prete
            }
        });
    }

    public static function generateClientCode(): string
    {
        if (!Schema::hasTable('clients')) {
            return 'CLI' . now()->format('Ym') . '0001';
        }

        $column = null;
        foreach (['client_code', 'code_client', 'code'] as $candidate) {
            if (Schema::hasColumn('clients', $candidate)) {
                $column = $candidate;
                break;
            }
        }

        if (!$column) {
            return 'CLI' . now()->format('Ym') . '0001';
        }

        $prefix = 'CLI' . now()->format('Ym');
        $lastCode = DB::table('clients')
            ->where($column, 'like', $prefix . '%')
            ->orderBy($column, 'desc')
            ->value($column);

        $lastSequence = $lastCode ? (int) substr((string) $lastCode, -4) : 0;

        return $prefix . str_pad((string) ($lastSequence + 1), 4, '0', STR_PAD_LEFT);
    }

    public function compteComptable(): BelongsTo
    {
        return $this->belongsTo(CompteComptable::class, 'compte_comptable_id');
    }

    public function getDisplayNameAttribute(): string
    {
        foreach (['nom', 'raison_sociale', 'company_name', 'nom_complet', 'contact_nom'] as $column) {
            $value = $this->getAttributeFromArray($column);
            if (!empty($value)) {
                return (string) $value;
            }
        }

        return 'Client #' . $this->getKey();
    }

    /**
     * Accesseur pour maintenir la compatibilité avec l'ancien code qui utilise "nom"
     * au lieu de "raison_sociale"
     *
     * @return string
     */
    public function getNomAttribute()
    {
        return $this->getDisplayNameAttribute();
    }
}
