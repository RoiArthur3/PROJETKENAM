<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Parcel;
use Illuminate\Support\Str;
use Carbon\Carbon;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'client')->get();

        if ($users->isEmpty()) {
            return;
        }

        // Créer des factures pour les utilisateurs
        foreach ($users as $user) {
            $parcels = Parcel::where('user_id', $user->id)
                ->inRandomOrder()
                ->take(rand(3, 8))
                ->get();

            if ($parcels->isEmpty()) {
                continue;
            }

            foreach ($parcels as $parcel) {
                $issueDate = Carbon::now()->subDays(rand(10, 180));
                $dueDate = $issueDate->copy()->addDays(30);
                $status = $this->getRandomInvoiceStatus($issueDate, $dueDate);

                $shipping = rand(5000, 50000) / 100;
                $customs = rand(0, 5000) / 100;
                $handling = rand(0, 3000) / 100;
                $insurance = rand(0, 3000) / 100;
                $other = rand(0, 2000) / 100;

                $subtotal = $shipping + $customs + $handling + $insurance + $other;
                $taxRate = 0.20;
                $taxAmount = $subtotal * $taxRate;
                $total = $subtotal + $taxAmount;
                $paidDate = $status === 'paid' ? $issueDate->copy()->addDays(rand(1, 20)) : null;

                Invoice::updateOrCreate(
                    ['parcel_id' => $parcel->id],
                    [
                        'user_id' => $user->id,
                        'invoice_number' => $this->generateInvoiceNumber($issueDate),
                        'shipping_cost' => $shipping,
                        'customs_fee' => $customs,
                        'handling_fee' => $handling,
                        'insurance_fee' => $insurance,
                        'other_fees' => $other,
                        'subtotal' => $subtotal,
                        'tax_amount' => $taxAmount,
                        'total_amount' => $total,
                        'currency' => 'EUR',
                        'status' => $status,
                        'issue_date' => $issueDate->toDateString(),
                        'due_date' => $dueDate->toDateString(),
                        'paid_date' => $paidDate?->toDateString(),
                        'notes' => $this->getRandomInvoiceNotes(),
                        'created_at' => $issueDate,
                        'updated_at' => $issueDate,
                    ]
                );
            }
        }
    }

    private function generateInvoiceNumber(Carbon $date): string
    {
        return 'INV-' . $date->format('Y') . '-' . str_pad($date->format('W'), 2, '0', STR_PAD_LEFT) . '-' . strtoupper(Str::random(4));
    }

    private function getRandomInvoiceStatus(Carbon $invoiceDate, Carbon $dueDate): string
    {
        $now = Carbon::now();

        if ($invoiceDate->gt($now->subDays(7))) {
            return 'draft';
        } elseif ($dueDate->lt($now)) {
            return rand(0, 3) === 0 ? 'cancelled' : 'paid';
        } else {
            $statuses = ['pending', 'paid'];
            return $statuses[array_rand($statuses)];
        }
    }

    private function getRandomInvoiceNotes(): ?string
    {
        $notes = [
            null,
            'Paiement par virement bancaire',
            'Paiement par carte bancaire',
            'Paiement par PayPal',
            'Échéancier de paiement mis en place',
            'Remise appliquée : fidélité client',
            'Remise appliquée : volume important',
            'Facture regroupée : plusieurs expéditions',
            'Client professionnel - TVA applicable',
            'Exonération de TVA - Export'
        ];
        return $notes[array_rand($notes)];
    }

    private function addInvoiceItems(Invoice $invoice): void
    {
        $items = [
            [
                'description' => 'Frais d\'expédition internationale',
                'quantity' => rand(1, 5),
                'unit_price' => rand(1500, 8000) / 100,
                'total' => 0
            ],
            [
                'description' => 'Assurance colis',
                'quantity' => 1,
                'unit_price' => rand(500, 2000) / 100,
                'total' => 0
            ],
            [
                'description' => 'Frais de douane',
                'quantity' => 1,
                'unit_price' => rand(1000, 5000) / 100,
                'total' => 0
            ],
            [
                'description' => 'Emballage renforcé',
                'quantity' => rand(1, 3),
                'unit_price' => rand(200, 1000) / 100,
                'total' => 0
            ],
            [
                'description' => 'Service de suivi prioritaire',
                'quantity' => 1,
                'unit_price' => rand(300, 1500) / 100,
                'total' => 0
            ]
        ];

        // Sélectionner 2-4 articles aléatoires
        $selectedItems = collect($items)->random(rand(2, 4));

        foreach ($selectedItems as $item) {
            $item['total'] = $item['quantity'] * $item['unit_price'];

            // Créer la ligne de facture (adapter selon votre modèle)
            // Si vous avez un modèle InvoiceItem, utilisez-le ici
            // InvoiceItem::create([...])
        }
    }
}
