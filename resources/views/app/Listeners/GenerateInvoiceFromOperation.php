<?php

namespace App\Listeners;

use App\Events\OperationCompleted;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class GenerateInvoiceFromOperation
{
    use InteractsWithQueue;

    protected InvoiceService $invoiceService;

    /**
     * Create the event listener.
     */
    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Handle the event.
     */
    public function handle(OperationCompleted $event): void
    {
        try {
            $operation = $event->operation;
            
            // Vérifier si l'opération a un client
            if (!$operation->client_id) {
                Log::info('Operation completed without client, skipping invoice generation', [
                    'operation_id' => $operation->id
                ]);
                return;
            }

            // Vérifier si une facture existe déjà
            if ($operation->invoice_id) {
                Log::info('Invoice already exists for operation', [
                    'operation_id' => $operation->id,
                    'invoice_id' => $operation->invoice_id
                ]);
                return;
            }

            // Générer la facture automatiquement
            $invoice = $this->invoiceService->createFromOperation($operation);
            
            // Lier la facture à l'opération
            $operation->update(['invoice_id' => $invoice->id]);

            Log::info('Invoice generated automatically from operation', [
                'operation_id' => $operation->id,
                'invoice_id' => $invoice->id,
                'invoice_reference' => $invoice->reference
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to generate invoice from operation', [
                'operation_id' => $event->operation->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
