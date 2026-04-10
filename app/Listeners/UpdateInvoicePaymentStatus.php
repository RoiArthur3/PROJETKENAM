<?php

namespace App\Listeners;

use App\Events\PaymentReceived;
use App\Models\Invoice;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdateInvoicePaymentStatus
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(PaymentReceived $event): void
    {
        try {
            $payment = $event->payment;
            $invoice = $payment->invoice;

            if (!$invoice) {
                Log::warning('Payment received without linked invoice', [
                    'payment_id' => $payment->id
                ]);
                return;
            }

            // Calculer le total payé et le solde
            $totalPaid = $invoice->payments()->sum('amount');
            $balanceDue = $invoice->total_amount - $totalPaid;

            // Déterminer le statut de paiement
            $paymentStatus = match (true) {
                $balanceDue <= 0 => 'paid',
                $totalPaid > 0 => 'partial',
                default => 'unpaid'
            };

            // Mettre à jour la facture
            $invoice->update([
                'amount_paid' => $totalPaid,
                'balance_due' => $balanceDue,
                'payment_status' => $paymentStatus
            ]);

            Log::info('Invoice payment status updated', [
                'invoice_id' => $invoice->id,
                'payment_id' => $payment->id,
                'total_paid' => $totalPaid,
                'balance_due' => $balanceDue,
                'payment_status' => $paymentStatus
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update invoice payment status', [
                'payment_id' => $event->payment->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
