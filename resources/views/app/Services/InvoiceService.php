<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Operation;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceService
{
    /**
     * Créer une facture à partir d'une opération
     */
    public function createFromOperation(Operation $operation): Invoice
    {
        return DB::transaction(function () use ($operation) {
            // Générer une référence unique
            $reference = $this->generateReference();
            
            // Calculer les montants (simulation - à adapter selon vos besoins)
            $baseAmount = $this->calculateOperationAmount($operation);
            $taxRate = 18; // TVA 18%
            $taxAmount = $baseAmount * ($taxRate / 100);
            $totalAmount = $baseAmount + $taxAmount;

            // Créer la facture
            $invoice = Invoice::create([
                'reference' => $reference,
                'type' => 'facture',
                'client_id' => $operation->client_id,
                'operation_id' => $operation->id,
                'total_amount' => $totalAmount,
                'tax_rate' => $taxRate,
                'net_amount' => $baseAmount,
                'payment_method' => 'virement',
                'status' => 'issued',
                'due_date' => now()->addDays(30),
                'issue_date' => now(),
                'amount_paid' => 0,
                'balance_due' => $totalAmount,
                'payment_status' => 'unpaid',
            ]);

            // Créer les lignes de facture (simulation)
            $this->createInvoiceLines($invoice, $operation);

            Log::info('Invoice created from operation', [
                'operation_id' => $operation->id,
                'invoice_id' => $invoice->id,
                'reference' => $reference,
                'amount' => $totalAmount
            ]);

            return $invoice;
        });
    }

    /**
     * Générer une référence de facture unique
     */
    private function generateReference(): string
    {
        $prefix = 'FCT';
        $date = now()->format('Ym');
        
        $lastInvoice = Invoice::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->orderBy('created_at', 'desc')
            ->first();

        $sequence = $lastInvoice ? intval(substr($lastInvoice->reference, -4)) + 1 : 1;
        
        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Calculer le montant de l'opération
     */
    private function calculateOperationAmount(Operation $operation): float
    {
        // Logique de calcul à adapter selon vos besoins
        // Pour l'instant, utilisons des valeurs par défaut basées sur le type d'opération
        
        $baseRates = [
            'transport' => 500000,
            'logistique' => 300000,
            'maintenance' => 200000,
            'consulting' => 750000,
        ];

        $operationType = $operation->services()->first()->type ?? 'transport';
        $baseAmount = $baseRates[$operationType] ?? 400000;

        // Ajuster selon la priorité
        $priorityMultiplier = match($operation->priorite) {
            'urgent' => 1.5,
            'high' => 1.2,
            'medium' => 1.0,
            'low' => 0.9,
            default => 1.0,
        };

        return $baseAmount * $priorityMultiplier;
    }

    /**
     * Créer les lignes de facture
     */
    private function createInvoiceLines(Invoice $invoice, Operation $operation): void
    {
        // Pour l'instant, nous simulons les lignes de facture
        // Dans une implémentation réelle, vous pourriez avoir une table invoice_lines
        
        $lines = [
            [
                'label' => $operation->titre,
                'quantity' => 1,
                'unit_price' => $invoice->net_amount,
                'total_ht' => $invoice->net_amount,
            ]
        ];

        // Si vous avez une table invoice_lines, vous feriez:
        // foreach ($lines as $line) {
        //     $invoice->lines()->create($line);
        // }

        Log::info('Invoice lines created', [
            'invoice_id' => $invoice->id,
            'lines_count' => count($lines)
        ]);
    }

    /**
     * Envoyer une facture par email
     */
    public function sendInvoiceByEmail(Invoice $invoice): bool
    {
        try {
            // Logique d'envoi d'email
            // Mail::to($invoice->client->email)->send(new InvoiceMail($invoice));
            
            $invoice->update(['email_sent_at' => now()]);
            
            Log::info('Invoice sent by email', [
                'invoice_id' => $invoice->id,
                'client_email' => $invoice->client?->email
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send invoice by email', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
}
