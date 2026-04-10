<?php

namespace App\Listeners;

use App\Events\OperationStatusChanged;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\Log;

class CreateInvoiceOnOperationComplete
{
    protected $invoiceService;

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
    public function handle(OperationStatusChanged $event): void
    {
        // Créer automatiquement une facture quand une opération est terminée
        if ($event->newStatus === 'terminee' && $event->oldStatus !== 'terminee') {
            try {
                $invoice = $this->invoiceService->createFromOperation($event->operation);
                
                // Mettre à jour le statut de paiement de l'opération
                $event->operation->update([
                    'statut_paiement' => 'en_attente',
                    'invoice_id' => $invoice->id
                ]);

                Log::info('Facture créée automatiquement pour l\'opération', [
                    'operation_id' => $event->operation->id,
                    'invoice_id' => $invoice->id
                ]);

            } catch (\Exception $e) {
                Log::error('Erreur lors de la création automatique de facture', [
                    'operation_id' => $event->operation->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Libérer les véhicules quand l'opération est terminée
        if ($event->newStatus === 'terminee') {
            $event->operation->vehicleAssignments()->update([
                'returned_at' => now(),
                'status' => 'terminee'
            ]);

            // Mettre à jour le statut des véhicules
            $event->operation->vehicleAssignments->each(function ($assignment) {
                $assignment->vehicle->update(['statut' => 'disponible']);
            });
        }
    }
}
