<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CommandesMissionPrestationExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function __construct(private readonly Collection $commandes)
    {
    }

    public function headings(): array
    {
        return [
            'Reference',
            'Fournisseur',
            'Base',
            'Objet',
            'Date commande',
            'Montant TTC',
            'Statut',
        ];
    }

    public function collection(): Collection
    {
        return $this->commandes->map(function ($commande) {
            $premiereLigne = $commande->lignes->first();

            return [
                'reference' => $commande->reference,
                'fournisseur' => $commande->fournisseur->raison_sociale ?? 'N/A',
                'base' => ucfirst((string) ($commande->nature_commande ?? 'autre')),
                'objet' => $premiereLigne->designation ?? 'Commande generale',
                'date_commande' => optional($commande->date_commande)->format('d/m/Y'),
                'montant_ttc' => (float) ($commande->montant_ttc ?? 0),
                'statut' => (string) $commande->statut,
            ];
        });
    }
}
