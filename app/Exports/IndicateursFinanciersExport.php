<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class IndicateursFinanciersExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function __construct(private readonly array $donnees)
    {
    }

    public function headings(): array
    {
        return ['Categorie', 'Indicateur', 'Valeur', 'Unite', 'Explication'];
    }

    public function array(): array
    {
        $rows = [
            ['Synthese', 'Chiffre d\'affaires', $this->donnees['chiffre_affaires'], 'FCFA', 'Produits retenus sur la période.'],
            ['Synthese', 'Encaissements', $this->donnees['encaissements'], 'FCFA', 'Flux encaissés validés.'],
            ['Synthese', 'Total charges', $this->donnees['total_charges'], 'FCFA', 'Charges opérationnelles agrégées.'],
            ['Synthese', 'Résultat net', $this->donnees['resultat_net'], 'FCFA', 'Résultat net sur la période.'],
            ['Synthese', 'Trésorerie disponible', $this->donnees['tresorerie_disponible'], 'FCFA', 'Banques, caisses et encaissements de la période.'],
            ['Synthese', 'Créances clients', $this->donnees['creances_clients'], 'FCFA', 'Factures impayées et en retard.'],
            ['Synthese', 'Dettes fournisseurs', $this->donnees['dettes_fournisseurs'], 'FCFA', 'Dettes court terme identifiées.'],
            ['Synthese', 'Besoin en fonds de roulement', $this->donnees['besoin_fonds_roulement'], 'FCFA', 'Créances moins dettes fournisseurs.'],
        ];

        foreach ($this->donnees['ratios_dgi'] as $ratio) {
            $rows[] = ['DGI', $ratio['libelle'], $ratio['valeur'], $this->resolveUnit($ratio['format']), $ratio['explication']];
        }

        foreach ($this->donnees['ratios_syscohada'] as $ratio) {
            $rows[] = ['SYSCOHADA', $ratio['libelle'], $ratio['valeur'], $this->resolveUnit($ratio['format']), $ratio['explication']];
        }

        return $rows;
    }

    private function resolveUnit(string $format): string
    {
        return match ($format) {
            'currency' => 'FCFA',
            'percent' => '%',
            'days' => 'jours',
            default => '',
        };
    }
}