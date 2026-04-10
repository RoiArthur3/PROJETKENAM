<?php

namespace App\Exports;

use App\Models\Fournisseur;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FournisseursExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(protected array $filters = [])
    {
    }

    public function collection()
    {
        $query = Fournisseur::query();
        
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where('nom', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('telephone', 'like', "%$search%");
        }
        
        if (!empty($this->filters['est_actif'])) {
            $query->where('est_actif', true);
        }
        
        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nom',
            'Email',
            'Téléphone',
            'Adresse',
            'Ville',
            'Pays',
            'Statut',
            'RCCM',
            'IFU',
            'Créé le',
        ];
    }

    public function map($fournisseur): array
    {
        return [
            $fournisseur->id,
            $fournisseur->nom,
            $fournisseur->email,
            $fournisseur->telephone,
            $fournisseur->adresse,
            $fournisseur->ville,
            $fournisseur->pays,
            $fournisseur->est_actif ? 'Actif' : 'Inactif',
            $fournisseur->rccm,
            $fournisseur->ifu,
            $fournisseur->created_at?->format('Y-m-d'),
        ];
    }
}
