@extends('layouts.app')

@section('title', 'Facture')
@section('subtitle', 'Facture')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Facture #{{ $invoice->id }}</h1>
            <p class="text-sm text-gray-500">Colis: {{ $invoice->parcel->tracking_number }}</p>
        </div>
        <div class="flex gap-2">
            @if(Auth::user() && Auth::user()->role === 'admin')
                <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn-outline-primary inline-flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                    Modifier
                </a>
            @endif
            <a href="{{ route('invoices.pdf', $invoice->id) }}" class="btn-primary inline-flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7,10 12,15 17,10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                Imprimer PDF
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Détails de la facture -->
        <div class="col-span-2 space-y-4">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-lg font-medium">Détails de la facture</h2>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Date</p>
                            <p class="font-medium">{{ $invoice->created_at->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Statut</p>
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                {{ $invoice->status }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Montant total</p>
                            <p class="font-medium">{{ number_format($invoice->total_amount, 2) }} XOF</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Montant dû</p>
                            @php
                                $paid = (float) ($invoice->payments?->where('status', 'completed')->sum('amount') ?? 0);
                                $due = max(0, (float) $invoice->total_amount - $paid);
                            @endphp
                            <p class="font-medium">{{ number_format($due, 2) }} XOF</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Détail des coûts -->
            <div class="card">
                <div class="card-header">
                    <h2 class="text-lg font-medium">Détail des coûts</h2>
                </div>
                <div class="card-body">
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b">
                            <span class="text-gray-600">Coût de transport</span>
                            <span class="font-medium">{{ number_format($invoice->shipping_cost, 2) }} XOF</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b">
                            <span class="text-gray-600 font-medium">Coût des cartons</span>
                            <span class="font-medium text-blue-600">{{ number_format($invoice->cartons_cost, 2) }} XOF</span>
                        </div>
                        <div class="flex justify-between items-center pt-3">
                            <span class="text-lg font-semibold">Total</span>
                            <span class="text-lg font-bold text-blue-600">{{ number_format($invoice->total_amount, 2) }} XOF</span>
                        </div>
                    </div>
                    @if($invoice->notes)
                        <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                            <p class="text-sm font-medium text-gray-700 mb-1">Notes:</p>
                            <p class="text-sm text-gray-600">{{ $invoice->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Informations de facturation -->
            <div class="card">
                <div class="card-header">
                    <h2 class="text-lg font-medium">Informations de facturation</h2>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Client</p>
                            <p class="font-medium">{{ $invoice->user->name }}</p>
                            @if($invoice->user->email)
                                <p class="text-sm text-gray-400">{{ $invoice->user->email }}</p>
                            @endif
                            @if($invoice->user->phone)
                                <p class="text-sm text-gray-400">{{ $invoice->user->phone }}</p>
                            @endif
                            @if($invoice->user->company_name)
                                <p class="text-sm text-blue-600">{{ $invoice->user->company_name }}</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Détails du colis</p>
                            <p class="font-medium">
                                📦 {{ $invoice->parcel->tracking_number }}
                            </p>
                            <p class="text-sm text-gray-400">
                                Dest: {{ $invoice->parcel->destination_country ?? 'Non spécifié' }}
                            </p>
                            @if($invoice->parcel->recipient_name)
                                <p class="text-sm text-gray-400">
                                    Destinataire: {{ $invoice->parcel->recipient_name }}
                                </p>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Nombre de cartons</p>
                            <p class="font-medium">{{ $invoice->parcel->cartons_count ?? 1 }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Poids total</p>
                            <p class="font-medium">{{ $invoice->parcel->weight ?? 'En attente de pesée' }} {{ $invoice->parcel->weight ? 'kg' : '' }}</p>
                        </div>
                        @if(str_contains($invoice->parcel->transport_mode ?? '', 'sea') || str_contains($invoice->parcel->transport_mode ?? '', 'boat'))
                        <div>
                            <p class="text-sm text-gray-500">Dimensions (L×l×H)</p>
                            <p class="font-medium">{{ $invoice->parcel->dimensions ?? 'Non spécifiées' }}</p>
                        </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-500">Mode d'envoi</p>
                            <p class="font-medium">
                                @switch($invoice->parcel->transport_mode)
                                    @case('air_express')
                                        ✈️ Avion Express
                                        @break
                                    @case('air_normal')
                                        ✈️ Avion Normal
                                        @break
                                    @case('sea')
                                    @case('boat')
                                        🚢 Bateau (Cargo)
                                        @break
                                    @default
                                        {{ $invoice->parcel->transport_mode }}
                                @endswitch
                            </p>
                        </div>
                        @if($invoice->parcel->content_description)
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-500">Contenu des cartons</p>
                            <p class="font-medium">{{ $invoice->parcel->content_description }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Paiements et actions -->
        <div class="space-y-4">
            @if($due > 0)
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-lg font-medium">Effectuer un paiement</h2>
                    </div>
                    <div class="card-body space-y-3">
                        <form action="{{ route('payments.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700" for="amount">Montant</label>
                                <input id="amount" name="amount" type="number" min="0.01" max="{{ $due }}" step="0.01" value="{{ $due }}" class="input-text" required>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700" for="method">Méthode de paiement</label>
                                <select id="method" name="method" class="input-select" required>
                                    <option value="card">Carte bancaire</option>
                                    <option value="bank_transfer">Virement</option>
                                    <option value="mobile_money">Mobile Money</option>
                                    <option value="paypal">PayPal</option>
                                    <option value="cash">Espèces</option>
                                </select>
                            </div>

                            <button type="submit" class="btn-primary w-full mt-2">
                                Payer
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Historique des paiements -->
            <div class="card">
                <div class="card-header">
                    <h2 class="text-lg font-medium">Paiements</h2>
                </div>
                <div class="card-body">
                    @if($invoice->payments->count() > 0)
                        <div class="divide-y divide-gray-200">
                            @foreach($invoice->payments as $payment)
                                <div class="py-3">
                                    <div class="flex justify-between">
                                        <p class="font-medium">{{ number_format($payment->amount, 2) }} XOF</p>
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">{{ $payment->payment_method }}</span>
                                    </div>
                                    <p class="text-sm text-gray-500">{{ $payment->created_at->format('d/m/Y H:i') }}</p>
                                    <div class="mt-2">
                                        <a href="{{ route('payments.receipt', $payment->id) }}" class="btn-outline-primary">Télécharger le reçu</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Aucun paiement effectué</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
