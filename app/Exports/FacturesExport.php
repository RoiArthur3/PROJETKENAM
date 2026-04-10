<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FacturesExport implements FromCollection, WithHeadings, WithMapping
{
    private array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection(): Collection
    {
        $schema = DB::getSchemaBuilder();
        if ($schema->hasColumn('clients', 'nom')) {
            $nameCol = 'nom';
        } elseif ($schema->hasColumn('clients', 'raison_sociale')) {
            $nameCol = 'raison_sociale';
        } elseif ($schema->hasColumn('clients', 'company_name')) {
            $nameCol = 'company_name';
        } else {
            $nameCol = 'contact_nom';
        }

        $query = DB::table('factures')
            ->join('clients', 'factures.client_id', '=', 'clients.id')
            ->select('factures.*', DB::raw("clients.{$nameCol} as client_nom"))
            ->orderByDesc('factures.created_at');

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search, $nameCol) {
                $q->where('factures.numero', 'like', '%' . $search . '%')
                    ->orWhere("clients.{$nameCol}", 'like', '%' . $search . '%');
            });
        }

        return $query->limit(5000)->get();
    }

    public function headings(): array
    {
        return [
            'Numéro',
            'Client',
            'Date',
            'Échéance',
            'Montant HT',
            'TVA',
            'Montant TTC',
            'Statut',
        ];
    }

    public function map($row): array
    {
        return [
            $row->numero ?? '',
            $row->client_nom ?? '',
            $row->date_facture ?? '',
            $row->date_echeance ?? '',
            $row->montant_ht ?? 0,
            $row->tva ?? 0,
            $row->montant_ttc ?? 0,
            $row->statut ?? '',
        ];
    }
}
