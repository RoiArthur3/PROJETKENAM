<?php

namespace App\Http\Controllers;

use App\Exports\ClientsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    /**
     * Exporter les clients en Excel
     */
    public function exportClients()
    {
        return Excel::download(new ClientsExport(), 'clients_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }

    /**
     * Export CSV d'une table autorisee (creances/caisse/dettes/param compta)
     */
    public function exportTableCsv(Request $request, string $table): StreamedResponse
    {
        $allowedTables = [
            'clients',
            'factures',
            'encaissements',
            'fournisseurs',
            'commande_fournisseurs',
            'facture_fournisseurs',
            'paiement_fournisseurs',
            'caisses',
            'mouvements_caisse',
            'comptes_comptables',
            'journal_comptables',
            'projets',
        ];

        if (!in_array($table, $allowedTables, true)) {
            abort(404, 'Table non autorisee pour export CSV.');
        }

        if (!Schema::hasTable($table)) {
            abort(404, 'Table introuvable.');
        }

        $filename = $table . '_' . now()->format('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function () use ($table) {
            $handle = fopen('php://output', 'w');

            // BOM UTF-8 pour une ouverture correcte dans Excel (Windows).
            fwrite($handle, "\xEF\xBB\xBF");

            $headers = Schema::getColumnListing($table);
            fputcsv($handle, $headers, ';');

            $query = DB::table($table);
            if (in_array('id', $headers, true)) {
                $query->orderBy('id');
            }

            $query->chunk(500, function ($rows) use ($handle, $headers) {
                foreach ($rows as $row) {
                    $rowArray = (array) $row;
                    $line = [];

                    foreach ($headers as $column) {
                        $line[] = $rowArray[$column] ?? null;
                    }

                    fputcsv($handle, $line, ';');
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
