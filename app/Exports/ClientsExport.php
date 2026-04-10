<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClientsExport implements FromCollection, WithHeadings, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Client::select(
            'id',
            'code',
            'raison_sociale',
            'contact_nom',
            'email',
            'telephone',
            'ville',
            'pays',
            'actif',
            'created_at'
        )->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Code',
            'Raison Sociale',
            'Contact Nom',
            'Email',
            'Téléphone',
            'Ville',
            'Pays',
            'Actif',
            'Créé le'
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF4472C4']], 'font' => ['color' => ['argb' => 'FFFFFFFF']]],
        ];
    }
}
