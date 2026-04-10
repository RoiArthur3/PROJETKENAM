<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Expense;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AccountingExportController extends Controller
{
    public function expensesCsv(Request $request): StreamedResponse
    {
        $filename = 'expenses_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Reference', 'Categorie', 'Description', 'Montant', 'Date', 'Fournisseur', 'Service', 'Statut']);

            Expense::orderByDesc('date_depense')->chunk(500, function ($chunk) use ($handle) {
                foreach ($chunk as $e) {
                    fputcsv($handle, [
                        $e->id,
                        $e->reference,
                        $e->categorie,
                        $e->description,
                        $e->montant,
                        optional($e->date_depense)->format('Y-m-d'),
                        $e->fournisseur,
                        $e->service_concerne,
                        $e->statut,
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }

    public function invoicesCsv(Request $request): StreamedResponse
    {
        $filename = 'invoices_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Invoice #', 'Client', 'Issue Date', 'Due Date', 'Net Amount', 'Status']);

            Invoice::orderByDesc('issue_date')->chunk(500, function ($chunk) use ($handle) {
                foreach ($chunk as $inv) {
                    fputcsv($handle, [
                        $inv->id,
                        $inv->invoice_number,
                        optional($inv->client)->name,
                        optional($inv->issue_date)->format('Y-m-d'),
                        optional($inv->due_date)->format('Y-m-d'),
                        $inv->net_amount,
                        $inv->status,
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }
}
