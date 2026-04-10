@extends('layouts.app')

@section('title', 'Reçu de paiement')
@section('subtitle', 'Reçu de paiement')

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl bg-white border border-gray-200 shadow-sm p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <div class="text-xs font-semibold text-blue-700">Reçu</div>
                <h1 class="mt-1 text-2xl font-semibold text-gray-900">Reçu de paiement</h1>
                <p class="mt-1 text-sm text-gray-500">Référence: {{ $payment->payment_reference }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('invoices.show', $payment->invoice_id) }}" class="btn-outline-primary rounded-xl px-4 py-3">Retour facture</a>
                <button type="button" onclick="window.print()" class="btn-primary rounded-xl px-4 py-3">Imprimer</button>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="text-sm font-semibold text-gray-900">Détails du paiement</div>
            <div class="text-xs text-blue-700">Document de confirmation pour le client</div>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                <div class="text-xs font-semibold text-gray-500 uppercase">Client</div>
                <div class="mt-1 text-sm font-semibold text-gray-900">{{ $payment->user?->name ?? '-' }}</div>
                <div class="text-xs text-gray-500">{{ $payment->user?->email ?? '-' }}</div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                <div class="text-xs font-semibold text-gray-500 uppercase">Facture</div>
                <div class="mt-1 text-sm font-semibold text-gray-900">{{ $payment->invoice?->invoice_number ?? ('#' . $payment->invoice_id) }}</div>
                <div class="text-xs text-gray-500">Statut facture: {{ $payment->invoice?->status ?? '-' }}</div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                <div class="text-xs font-semibold text-gray-500 uppercase">Montant</div>
                <div class="mt-1 text-sm font-semibold text-gray-900">{{ number_format($payment->amount, 2) }} {{ $payment->currency ?? 'EUR' }}</div>
                <div class="text-xs text-gray-500">Statut paiement: {{ $payment->status }}</div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                <div class="text-xs font-semibold text-gray-500 uppercase">Méthode</div>
                <div class="mt-1 text-sm font-semibold text-gray-900">{{ $payment->payment_method }}</div>
                <div class="text-xs text-gray-500">Payé le: {{ $payment->paid_at ?? $payment->created_at }}</div>
            </div>

            <div class="md:col-span-2 rounded-xl border border-gray-200 bg-gray-50 p-4">
                <div class="text-xs font-semibold text-gray-500 uppercase">Colis</div>
                <div class="mt-1 text-sm font-semibold text-gray-900">{{ $payment->invoice?->parcel?->tracking_number ?? '-' }}</div>
                <div class="text-xs text-gray-500">{{ $payment->invoice?->parcel?->origin_country ?? '' }} {{ $payment->invoice?->parcel?->destination_country ? ('→ ' . $payment->invoice?->parcel?->destination_country) : '' }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
