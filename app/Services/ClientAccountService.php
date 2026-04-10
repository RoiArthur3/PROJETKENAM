<?php

namespace App\Services;

use App\Models\Client;
use App\Models\CompteComptable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClientAccountService
{
    public function canManageClientAccounts(): bool
    {
        return Schema::hasTable('clients')
            && Schema::hasTable('comptes_comptables')
            && Schema::hasColumn('clients', 'compte_comptable_id');
    }

    public function ensureClientAccountById(int $clientId): ?CompteComptable
    {
        $client = Client::query()->find($clientId);

        if (!$client) {
            return null;
        }

        return $this->ensureForClient($client);
    }

    public function ensureForClient(Client $client): ?CompteComptable
    {
        if (!$this->canManageClientAccounts()) {
            return null;
        }

        $currentAccountId = (int) ($client->getAttribute('compte_comptable_id') ?? 0);
        if ($currentAccountId > 0) {
            return CompteComptable::query()->find($currentAccountId);
        }

        $account = $this->createAuxiliaryClientAccount($client);

        $clientUpdate = [
            'compte_comptable_id' => $account->getKey(),
        ];

        if ($this->hasClientTimestampColumns()) {
            $clientUpdate['updated_at'] = now();
        }

        DB::table('clients')
            ->where('id', $client->getKey())
            ->update($clientUpdate);

        $client->setAttribute('compte_comptable_id', $account->getKey());

        return $account;
    }

    private function createAuxiliaryClientAccount(Client $client): CompteComptable
    {
        $accountNumber = $this->nextClientAccountNumber();
        $label = $this->buildClientAccountLabel($client);

        $payload = [];

        if (Schema::hasColumn('comptes_comptables', 'numero')) {
            $payload['numero'] = $accountNumber;
        }

        if (Schema::hasColumn('comptes_comptables', 'numero_compte')) {
            $payload['numero_compte'] = $accountNumber;
        }

        if (Schema::hasColumn('comptes_comptables', 'code')) {
            $payload['code'] = $accountNumber;
        }

        if (Schema::hasColumn('comptes_comptables', 'libelle')) {
            $payload['libelle'] = $label;
        }

        if (Schema::hasColumn('comptes_comptables', 'intitule')) {
            $payload['intitule'] = $label;
        }

        if (Schema::hasColumn('comptes_comptables', 'description')) {
            $payload['description'] = 'Compte auxiliaire client genere automatiquement';
        }

        if (Schema::hasColumn('comptes_comptables', 'type')) {
            $payload['type'] = 'actif';
        }

        if (Schema::hasColumn('comptes_comptables', 'actif')) {
            $payload['actif'] = true;
        }

        if (Schema::hasColumn('comptes_comptables', 'est_verrouille')) {
            $payload['est_verrouille'] = false;
        }

        if (Schema::hasColumn('comptes_comptables', 'parent_id')) {
            $parentId = $this->findClientParentAccountId();
            if ($parentId) {
                $payload['parent_id'] = $parentId;
            }
        }

        return CompteComptable::query()->create($payload);
    }

    private function nextClientAccountNumber(): string
    {
        $column = $this->getAccountNumberColumn();

        if (!$column) {
            return '411001';
        }

        $numbers = CompteComptable::query()
            ->whereNotNull($column)
            ->pluck($column)
            ->filter(fn ($value) => is_string($value) || is_numeric($value))
            ->map(fn ($value) => preg_replace('/\D+/', '', (string) $value))
            ->filter(fn ($value) => str_starts_with($value, '411'))
            ->map(fn ($value) => (int) $value);

        $max = $numbers->max() ?? 411000;

        return (string) ($max + 1);
    }

    private function buildClientAccountLabel(Client $client): string
    {
        $name = trim((string) ($client->display_name ?? $client->nom ?? $client->raison_sociale ?? $client->company_name ?? $client->nom_complet ?? ('Client #' . $client->getKey())));

        return 'Client - ' . $name;
    }

    private function getAccountNumberColumn(): ?string
    {
        foreach (['numero', 'numero_compte', 'code'] as $column) {
            if (Schema::hasColumn('comptes_comptables', $column)) {
                return $column;
            }
        }

        return null;
    }

    private function findClientParentAccountId(): ?int
    {
        $column = $this->getAccountNumberColumn();

        if (!$column) {
            return null;
        }

        $parent = CompteComptable::query()
            ->where($column, '411')
            ->orWhere($column, '411000')
            ->first();

        return $parent ? (int) $parent->getKey() : null;
    }

    private function hasClientTimestampColumns(): bool
    {
        return Schema::hasColumn('clients', 'updated_at');
    }
}