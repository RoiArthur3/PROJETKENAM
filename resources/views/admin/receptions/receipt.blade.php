@extends('layouts.app')

@section('title', 'Reçu de dépôt')
@section('subtitle', 'Reçu de dépôt')

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl bg-white border border-gray-200 shadow-sm p-6 print:hidden">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <div class="text-xs font-semibold text-blue-700">Entrepôt</div>
                <h1 class="mt-1 text-2xl font-semibold text-gray-900">Reçu de dépôt</h1>
                <p class="mt-1 text-sm text-gray-500">Colis: <span class="font-semibold">{{ $parcel->tracking_number }}</span></p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.receptions.show', $parcel->id) }}" class="btn-outline-primary rounded-xl px-4 py-3">Retour</a>
                <button type="button" onclick="window.print()" class="btn-primary rounded-xl px-4 py-3">Imprimer</button>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="text-sm font-semibold text-gray-900">Confirmation de dépôt</div>
            <div class="text-xs text-blue-700">Document remis lors du dépôt du colis</div>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                <div class="text-xs font-semibold text-gray-500 uppercase">Numéro de suivi</div>
                <div class="mt-1 text-lg font-semibold text-gray-900">{{ $parcel->tracking_number }}</div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                <div class="text-xs font-semibold text-gray-500 uppercase">Date/heure du dépôt</div>
                <div class="mt-1 text-sm font-semibold text-gray-900">{{ now() }}</div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                <div class="text-xs font-semibold text-gray-500 uppercase">Client</div>
                <div class="mt-1 text-sm font-semibold text-gray-900">{{ $parcel->user?->name ?? '-' }}</div>
                <div class="text-xs text-gray-500">{{ $parcel->user?->email ?? '-' }}</div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                <div class="text-xs font-semibold text-gray-500 uppercase">Itinéraire</div>
                <div class="mt-1 text-sm font-semibold text-gray-900">{{ $parcel->origin_country ?? '-' }} → {{ $parcel->destination_country ?? '-' }}</div>
                <div class="text-xs text-gray-500">Mode: {{ $parcel->transport_mode ?? '-' }}</div>
            </div>

            <div class="md:col-span-2 rounded-xl border border-gray-200 bg-gray-50 p-4">
                <div class="text-xs font-semibold text-gray-500 uppercase">Détails colis</div>
                <div class="mt-2 grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <div>
                        <div class="text-xs text-gray-500">Poids déclaré</div>
                        <div class="text-sm font-semibold text-gray-900">{{ $parcel->estimated_weight ? $parcel->estimated_weight . ' kg' : '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Poids réel</div>
                        <div class="text-sm font-semibold text-gray-900">{{ $parcel->actual_weight ? $parcel->actual_weight . ' kg' : '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Dimensions</div>
                        <div class="text-sm font-semibold text-gray-900">{{ $parcel->length && $parcel->width && $parcel->height ? $parcel->length . '×' . $parcel->width . '×' . $parcel->height . ' cm' : '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Statut</div>
                        <div class="text-sm font-semibold text-gray-900">{{ $parcel->status }}</div>
                    </div>
                </div>
            </div>

            <div class="md:col-span-2 rounded-xl border border-gray-200 bg-white p-4">
                <div class="text-xs font-semibold text-gray-500 uppercase">Remarque</div>
                <div class="mt-1 text-sm text-gray-900">Ce reçu confirme le dépôt du colis à l'entrepôt. La facturation définitive dépendra du poids/dimensions réels et des frais applicables.</div>
            </div>
        </div>
    </div>
</div>
@endsection
