<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AnalyseActiviteExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function __construct(private readonly array $donnees)
    {
    }

    public function headings(): array
    {
        return [
            'Poste',
            (string) ($this->donnees['annee_n1'] ?? 'N-1'),
            (string) ($this->donnees['annee_n'] ?? 'N'),
            'Variation',
            'Variation %',
        ];
    }

    public function array(): array
    {
        $rows = [];

        foreach (($this->donnees['lignes'] ?? []) as $ligne) {
            $key = $ligne['key'];
            $n1 = (float) (($this->donnees['n1'][$key] ?? 0));
            $n = (float) (($this->donnees['n'][$key] ?? 0));
            $variation = $n - $n1;
            $variationPct = $n1 != 0 ? ($variation / abs($n1)) * 100 : null;

            $rows[] = [
                $ligne['libelle'],
                round($n1, 2),
                round($n, 2),
                round($variation, 2),
                $variationPct === null ? null : round($variationPct, 2),
            ];
        }

        return $rows;
    }
}
