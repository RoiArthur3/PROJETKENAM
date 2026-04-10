<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture #{{ $invoice->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            font-size: 14px;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .invoice-info div {
            width: 48%;
        }
        .invoice-info h3 {
            margin: 0 0 10px 0;
            font-size: 16px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .invoice-info p {
            margin: 5px 0;
        }
        .product-details {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .product-details h4 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #495057;
        }
        .tracking-number {
            background-color: #e3f2fd;
            border: 1px solid #90caf9;
            padding: 10px;
            text-align: center;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .tracking-number .number {
            font-size: 18px;
            font-weight: bold;
            color: #1976d2;
            letter-spacing: 2px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .items-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .items-table .text-right {
            text-align: right;
        }
        .totals {
            text-align: right;
            margin-top: 20px;
        }
        .totals table {
            width: 300px;
            border-collapse: collapse;
            margin-left: auto;
        }
        .totals td {
            padding: 5px;
            border: none;
        }
        .totals .total-row td {
            border-top: 2px solid #333;
            font-weight: bold;
            padding-top: 10px;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 11px;
            color: #666;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-paid {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-overdue {
            background-color: #f8d7da;
            color: #721c24;
        }
        .withdrawal-info {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .withdrawal-info h4 {
            margin: 0 0 10px 0;
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>FACTURE</h1>
        <p>#{{ $invoice->invoice_number ?? $invoice->id }}</p>
        <p>Date: {{ $invoice->created_at->format('d/m/Y') }}</p>
    </div>

    @if(Auth::user()->role === 'client' && in_array($invoice->parcel->status, ['announced', 'declared']))
        <div class="withdrawal-info">
            <h4>📋 FACTURE PROVISOIRE - ATTENTE DE RÉCEPTION</h4>
            <p>Cette facture sera définitive après réception et pesée réelle de votre colis.</p>
            <p>Le montant final peut être ajusté selon le poids réel.</p>
        </div>
    @endif

    <div class="tracking-number">
        <strong>NUMÉRO DE SUIVI</strong><br>
        <span class="number">{{ $invoice->parcel->tracking_number }}</span>
    </div>

    <div class="invoice-info">
        <div>
            <h3>Facturé à:</h3>
            <p><strong>{{ $invoice->user->name }}</strong></p>
            @if($invoice->user->company_name)
                <p>{{ $invoice->user->company_name }}</p>
            @endif
            <p>{{ $invoice->user->email }}</p>
            @if($invoice->user->phone)
                <p>{{ $invoice->user->phone }}</p>
            @endif
            @if($invoice->user->address)
                <p>{{ $invoice->user->address }}</p>
            @endif
        </div>
        <div>
            <h3>Détails de la facture:</h3>
            <p>Date d'émission: {{ $invoice->created_at->format('d/m/Y') }}</p>
            @if($invoice->due_date)
                <p>Date d'échéance: {{ $invoice->due_date->format('d/m/Y') }}</p>
            @endif
            <p>Statut:
                <span class="status-badge
                    @if($invoice->status === 'paid') status-paid
                    @elseif($invoice->status === 'pending') status-pending
                    @else status-overdue @endif">
                    {{ $invoice->status }}
                </span>
            </p>
        </div>
    </div>

    <div class="product-details">
        <h4>📦 Informations de facturation</h4>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 5px;"><strong>Numéro de suivi:</strong></td>
                <td style="padding: 5px;">{{ $invoice->parcel->tracking_number }}</td>
            </tr>
            <tr>
                <td style="padding: 5px;"><strong>Destination:</strong></td>
                <td style="padding: 5px;">
                    {{ $invoice->parcel->destination_city ? $invoice->parcel->destination_city . ', ' : '' }}
                    {{ $invoice->parcel->destination_country ?? 'Non spécifiée' }}
                </td>
            </tr>
            @if($invoice->parcel->recipient_name)
            <tr>
                <td style="padding: 5px;"><strong>Destinataire:</strong></td>
                <td style="padding: 5px;">{{ $invoice->parcel->recipient_name }}</td>
            </tr>
            @endif
            <tr>
                <td style="padding: 5px;"><strong>Type de produits:</strong></td>
                <td style="padding: 5px;">{{ $invoice->parcel->content_type ?? 'Non spécifié' }}</td>
            </tr>
            <tr>
                <td style="padding: 5px;"><strong>Nombre de cartons:</strong></td>
                <td style="padding: 5px;">{{ $invoice->parcel->cartons_count ?? 1 }}</td>
            </tr>
            <tr>
                <td style="padding: 5px;"><strong>Poids total:</strong></td>
                <td style="padding: 5px;">{{ $invoice->parcel->weight ?? 'En attente de pesée' }} {{ $invoice->parcel->weight ? 'kg' : '' }}</td>
            </tr>
            @if($invoice->parcel->transport_mode === 'sea')
            <tr>
                <td style="padding: 5px;"><strong>Dimensions (L×l×H):</strong></td>
                <td style="padding: 5px;">{{ $invoice->parcel->dimensions ?? 'Non spécifiées' }}</td>
            </tr>
            @endif
            <tr>
                <td style="padding: 5px;"><strong>Mode d'envoi:</strong></td>
                <td style="padding: 5px;">
                    @switch($invoice->parcel->transport_mode)
                        @case('air_express')
                            ✈️ Avion Express
                            @break
                        @case('air_normal')
                            ✈️ Avion Normal
                            @break
                        @case('sea')
                            🚢 Bateau (Cargo)
                            @break
                        @default
                            {{ $invoice->parcel->transport_mode }}
                    @endswitch
                </td>
            </tr>
            @if($invoice->parcel->content_description)
            <tr>
                <td style="padding: 5px;"><strong>Description:</strong></td>
                <td style="padding: 5px;">{{ $invoice->parcel->content_description }}</td>
            </tr>
            @endif
        </table>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Coût total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Coût de transport</td>
                <td class="text-right">{{ number_format($invoice->shipping_cost, 2) }} {{ $invoice->currency ?? 'XOF' }}</td>
            </tr>
            @if($invoice->cartons_cost > 0)
            <tr>
                <td>Coût des cartons</td>
                <td class="text-right">{{ number_format($invoice->cartons_cost, 2) }} {{ $invoice->currency ?? 'XOF' }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr class="total-row">
                <td>Total à payer:</td>
                <td class="text-right">{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency ?? 'XOF' }}</td>
            </tr>
            @php
                $paid = (float) ($invoice->payments?->where('status', 'completed')->sum('amount') ?? 0);
                $due = max(0, (float) $invoice->total_amount - $paid);
            @endphp
            @if($paid > 0)
            <tr>
                <td>Montant payé:</td>
                <td class="text-right">{{ number_format($paid, 2) }} {{ $invoice->currency ?? 'XOF' }}</td>
            </tr>
            @endif
            @if($due > 0)
            <tr>
                <td>Montant dû:</td>
                <td class="text-right">{{ number_format($due, 2) }} {{ $invoice->currency ?? 'XOF' }}</td>
            </tr>
            @endif
        </table>
    </div>

    @if($invoice->notes)
    <div style="margin-top: 30px;">
        <h3>Notes:</h3>
        <p>{{ $invoice->notes }}</p>
    </div>
    @endif

    @if(Auth::user()->role === 'client')
    <div class="withdrawal-info" style="margin-top: 30px;">
        <h4>📍 INSTRUCTIONS POUR RETRAIT</h4>
        <p>Présentez cette facture et votre pièce d'identité pour retirer votre colis.</p>
        <p>Numéro de suivi à communiquer: <strong>{{ $invoice->parcel->tracking_number }}</strong></p>
    </div>
    @endif

    <div class="footer">
        <p>Merci pour votre confiance!</p>
        <p>Cette facture a été générée automatiquement le {{ now()->format('d/m/Y H:i') }}</p>
        @if(Auth::user()->role === 'client')
            <p><strong>Document requis pour le retrait du colis</strong></p>
        @endif
    </div>
</body>
</html>
