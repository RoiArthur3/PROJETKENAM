<x-dashboard-layout title="Détails du Bon de Livraison" icon="fa-solid fa-truck">
    <x-slot name="kpis">
        <x-kpi-card title="Total Bons" value="{{ DB::table('bons_livraison')->count() }}" icon="fa-solid fa-truck" color="blue" />
        <x-kpi-card title="En Préparation" value="{{ DB::table('bons_livraison')->where('statut', 'en_preparation')->count() }}" icon="fa-solid fa-box" color="yellow" />
        <x-kpi-card title="En Transit" value="{{ DB::table('bons_livraison')->where('statut', 'en_transit')->count() }}" icon="fa-solid fa-shipping-fast" color="orange" />
        <x-kpi-card title="Livrés" value="{{ DB::table('bons_livraison')->where('statut', 'livre')->count() }}" icon="fa-solid fa-check-circle" color="green" />
    </x-slot>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fa-solid fa-truck me-2"></i>
                        Bon de Livraison {{ $bonLivraison->numero }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('commercial.bons-livraison.edit', $bonLivraison->id) }}" class="btn btn-warning btn-sm">
                            <i class="fa-solid fa-edit me-1"></i>
                            Modifier
                        </a>
                        <a href="{{ route('commercial.bons-livraison.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fa-solid fa-arrow-left me-1"></i>
                            Retour à la liste
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Informations principales -->
                        <div class="col-md-6">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        <i class="fa-solid fa-info-circle me-2"></i>
                                        Informations Générales
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Numéro :</strong></td>
                                            <td><span class="badge bg-primary">{{ $bonLivraison->numero }}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Bon de commande :</strong></td>
                                            <td><span class="badge bg-info">{{ $bonLivraison->bon_commande_numero }}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Client :</strong></td>
                                            <td>{{ $bonLivraison->client_nom }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email client :</strong></td>
                                            <td>{{ $bonLivraison->client_email }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Date livraison :</strong></td>
                                            <td>{{ \Carbon\Carbon::parse($bonLivraison->date_livraison)->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Livreur :</strong></td>
                                            <td>{{ $bonLivraison->livreur ?? 'Non assigné' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Statut :</strong></td>
                                            <td>
                                                @switch($bonLivraison->statut)
                                                    @case('en_preparation')
                                                        <span class="badge bg-warning">En préparation</span>
                                                    @break
                                                    @case('en_transit')
                                                        <span class="badge bg-info">En transit</span>
                                                    @break
                                                    @case('livre')
                                                        <span class="badge bg-success">Livre</span>
                                                    @break
                                                    @case('retourne')
                                                        <span class="badge bg-secondary">Retourné</span>
                                                    @break
                                                    @case('annule')
                                                        <span class="badge bg-danger">Annulé</span>
                                                    @break
                                                @endswitch
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Adresse de livraison -->
                        <div class="col-md-6">
                            <div class="card card-outline card-success">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        <i class="fa-solid fa-map-marker-alt me-2"></i>
                                        Adresse de Livraison
                                    </h4>
                                </div>
                                <div class="card-body">
                                    @if($bonLivraison->adresse_livraison)
                                        <p class="mb-0">{{ nl2br($bonLivraison->adresse_livraison) }}</p>
                                    @else
                                        <p class="text-muted mb-0">Adresse du client</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes et observations -->
                    @if($bonLivraison->notes || $bonLivraison->observations)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card card-outline card-info">
                                    <div class="card-header">
                                        <h4 class="card-title">
                                            <i class="fa-solid fa-sticky-note me-2"></i>
                                            Notes et Observations
                                        </h4>
                                    </div>
                                    <div class="card-body">
                                        @if($bonLivraison->notes)
                                            <div class="mb-3">
                                                <h6><i class="fa-solid fa-clipboard me-2"></i>Notes :</h6>
                                                <p class="mb-0">{{ nl2br($bonLivraison->notes) }}</p>
                                            </div>
                                        @endif

                                        @if($bonLivraison->observations)
                                            <div>
                                                <h6><i class="fa-solid fa-comment me-2"></i>Observations :</h6>
                                                <p class="mb-0">{{ nl2br($bonLivraison->observations) }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Informations de création -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card card-outline card-secondary">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        <i class="fa-solid fa-history me-2"></i>
                                        Historique
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1">
                                                <strong>Créé par :</strong>
                                                {{ $bonLivraison->created_by_name ?? 'Système' }}
                                            </p>
                                            <p class="mb-0">
                                                <strong>Date de création :</strong>
                                                {{ \Carbon\Carbon::parse($bonLivraison->created_at)->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            @if($bonLivraison->delivered_by_name)
                                                <p class="mb-1">
                                                    <strong>Livré par :</strong>
                                                    {{ $bonLivraison->delivered_by_name }}
                                                </p>
                                            @endif
                                            <p class="mb-0">
                                                <strong>Dernière modification :</strong>
                                                {{ \Carbon\Carbon::parse($bonLivraison->updated_at)->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <a href="{{ route('commercial.bons-livraison.index') }}" class="btn btn-secondary">
                                        <i class="fa-solid fa-arrow-left me-2"></i>
                                        Retour à la liste
                                    </a>
                                </div>
                                <div>
                                    <button onclick="window.print()" class="btn btn-outline-primary me-2">
                                        <i class="fa-solid fa-print me-2"></i>
                                        Imprimer
                                    </button>
                                    <a href="{{ route('commercial.bons-livraison.edit', $bonLivraison->id) }}" class="btn btn-warning">
                                        <i class="fa-solid fa-edit me-2"></i>
                                        Modifier
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>

@push('styles')
<style>
@media print {
    .card-tools, .no-print {
        display: none !important;
    }
    .card {
        break-inside: avoid;
    }
}
</style>
@endpush
