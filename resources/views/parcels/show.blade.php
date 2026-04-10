@extends('layouts.app')

@section('title', 'Détails du colis')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Détails du colis</h1>
            <p class="text-sm text-gray-500">Numéro de suivi: {{ $parcel->tracking_number }}</p>
        </div>
        <div>
            <span class="px-3 py-1 text-sm rounded-full {{ $parcel->status_color }}">
                {{ $parcel->status }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Informations de base -->
        <div class="col-span-2 space-y-4">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-lg font-medium">Informations du colis</h2>
                </div>
                <div class="card-body space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Mode de transport</p>
                            <p class="font-medium">{{ $parcel->transport_mode_label }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Pays d'origine</p>
                            <p class="font-medium">{{ $parcel->origin_country }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Pays de destination</p>
                            <p class="font-medium">{{ $parcel->destination_country }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Date de création</p>
                            <p class="font-medium">{{ $parcel->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Description du contenu</p>
                        <p class="font-medium">{{ $parcel->content_description ?? 'Non spécifié' }}</p>
                    </div>
                </div>
            </div>

            <!-- Historique des statuts -->
            <div class="card">
                <div class="card-header">
                    <h2 class="text-lg font-medium">Historique</h2>
                </div>
                <div class="card-body">
                    <div class="space-y-4">
                        @foreach($parcel->statusHistory as $history)
                            <div class="flex items-start">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">{{ $history->new_status_label }}</p>
                                    <p class="text-sm text-gray-500">{{ $history->created_at->format('d/m/Y H:i') }}</p>
                                    <p class="text-sm text-gray-500 mt-1">{{ $history->comment }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Photos et actions -->
        <div class="space-y-4">
            <!-- Photos -->
            <div class="card">
                <div class="card-header">
                    <h2 class="text-lg font-medium">Photos</h2>
                </div>
                <div class="card-body">
                    @if($parcel->photos->count() > 0)
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($parcel->photos as $photo)
                                <img src="{{ asset('storage/' . $photo->file_path) }}" alt="Photo du colis" class="rounded-lg object-cover h-32 w-full">
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Aucune photo disponible</p>
                    @endif
                </div>
            </div>

            <!-- Facture -->
            @if($parcel->invoice)
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-lg font-medium">Facture</h2>
                    </div>
                    <div class="card-body">
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <p class="text-sm text-gray-500">Montant total</p>
                                <p class="font-medium">{{ number_format($parcel->invoice->total_amount, 2) }} €</p>
                            </div>
                            <div class="flex justify-between">
                                <p class="text-sm text-gray-500">Statut</p>
                                <span class="px-2 py-1 text-xs rounded-full {{ $parcel->invoice->status_color }}">
                                    {{ $parcel->invoice->status_label }}
                                </span>
                            </div>
                            <a href="{{ route('invoices.show', $parcel->invoice->id) }}" class="btn-primary w-full mt-2">
                                Voir la facture
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
