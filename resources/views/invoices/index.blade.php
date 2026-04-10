@extends('layouts.app')

@section('title', 'Mes factures')
@section('subtitle', 'Mes factures')

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl bg-white border border-gray-200 shadow-sm p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Mes factures</h1>
                <p class="mt-1 text-sm text-blue-700">Consulter les factures et les paiements associés.</p>
                @if(Auth::check())
                    <p class="mt-1 text-xs text-gray-500">Rôle: {{ Auth::user()->role }}</p>
                @endif
            </div>
            @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'agent']))
                <a href="{{ route('invoices.create') }}" class="btn-primary inline-flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Émettre une facture
                </a>
            @endif
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="text-sm font-semibold text-gray-900">Liste des factures</div>
            <div class="text-xs text-blue-700">Ouvre une facture pour payer et télécharger les reçus.</div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left text-xs font-semibold text-gray-600 px-6 py-3">N°</th>
                        <th class="text-left text-xs font-semibold text-gray-600 px-6 py-3">Colis</th>
                        <th class="text-left text-xs font-semibold text-gray-600 px-6 py-3">Statut</th>
                        <th class="text-left text-xs font-semibold text-gray-600 px-6 py-3">Total</th>
                        <th class="text-left text-xs font-semibold text-gray-600 px-6 py-3">Créée</th>
                        <th class="text-right text-xs font-semibold text-gray-600 px-6 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $invoice->invoice_number ?? ('#' . $invoice->id) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $invoice->parcel?->tracking_number ?? '-' }}</div>
                                <div class="text-xs text-blue-700">{{ $invoice->parcel?->origin_country ?? '' }} {{ $invoice->parcel?->destination_country ? ('→ ' . $invoice->parcel?->destination_country) : '' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $paid = (float) ($invoice->payments?->where('status', 'completed')->sum('amount') ?? 0);
                                    $due = max(0, (float) $invoice->total_amount - $paid);
                                    $isFullyPaid = $due <= 0;
                                @endphp

                                @if($isFullyPaid)
                                    <span class="inline-flex items-center rounded-full border border-green-200 bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Payée
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full border border-orange-200 bg-orange-50 px-3 py-1 text-xs font-semibold text-orange-700">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        Non payée
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900"><span data-money="{{ $invoice->total_amount }}" data-money-currency="{{ $invoice->currency ?? 'EUR' }}"></span></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $invoice->created_at }}</div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('invoices.show', $invoice->id) }}" class="inline-flex items-center justify-center rounded-xl bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Ouvrir</a>

                                    @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'agent']))
                                        @if($isFullyPaid)
                                            <form action="{{ route('invoices.mark-unpaid', $invoice->id) }}" method="POST" onsubmit="return confirm('Marquer cette facture comme non payée ?')">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-orange-600 px-3 py-2 text-sm font-semibold text-white hover:bg-orange-700" title="Marquer comme non payée">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('invoices.mark-paid', $invoice->id) }}" method="POST" onsubmit="return confirm('Marquer cette facture comme payée ?')">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-green-600 px-3 py-2 text-sm font-semibold text-white hover:bg-green-700" title="Marquer comme payée">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center">
                                <div class="text-sm font-semibold text-gray-900">Aucune facture</div>
                                <div class="text-sm text-blue-700 mt-1">Les factures apparaîtront ici après calcul des frais.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $invoices->links() }}
        </div>
    </div>
</div>
@endsection
