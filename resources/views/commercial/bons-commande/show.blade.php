<x-dashboard-layout title="Détails du Bon de Commande" icon="fa-solid fa-file-invoice">
    <x-slot name="kpis">
        <x-kpi-card title="Total Bons" value="{{ DB::table('bons_commande')->count() }}" icon="fa-solid fa-file-invoice" color="blue" />
        <x-kpi-card title="En Attente" value="{{ DB::table('bons_commande')->where('statut', 'brouillon')->count() }}" icon="fa-solid fa-clock" color="yellow" />
        <x-kpi-card title="Validés" value="{{ DB::table('bons_commande')->where('statut', 'valide')->count() }}" icon="fa-solid fa-check-circle" color="green" />
        <x-kpi-card title="Livrés" value="{{ DB::table('bons_commande')->where('statut', 'livre')->count() }}" icon="fa-solid fa-truck" color="emerald" />
    </x-slot>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fa-solid fa-file-invoice me-2"></i>
                        Bon de Commande {{ $bonCommande->numero }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('commercial.bons-commande.edit', $bonCommande->id) }}" class="btn btn-warning btn-sm">
                            <i class="fa-solid fa-edit me-1"></i>
                            Modifier
                        </a>
                        <a href="{{ route('materiel.missions.create', [
                            'source_type' => 'bons_commande',
                            'source_id' => $bonCommande->id,
                            'source_reference' => $bonCommande->numero,
                            'client_id' => $bonCommande->client_id,
                            'start_at' => $bonCommande->date_commande,
                            'end_at' => $bonCommande->date_livraison_prevue,
                        ]) }}" class="btn btn-info btn-sm">
                            <i class="fa-solid fa-truck me-1"></i>
                            Créer mission engin
                        </a>
                        <a href="{{ route('commercial.bons-commande.index') }}" class="btn btn-secondary btn-sm">
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
                                            <td><span class="badge bg-primary">{{ $bonCommande->numero }}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Client :</strong></td>
                                            <td>{{ $bonCommande->client_nom }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email client :</strong></td>
                                            <td>{{ $bonCommande->client_email }}</td>
                                        </tr>
                                        @if($bonCommande->contrat_numero)
                                            <tr>
                                                <td><strong>Contrat associé :</strong></td>
                                                <td><span class="badge bg-info">{{ $bonCommande->contrat_numero }}</span></td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td><strong>Date commande :</strong></td>
                                            <td>{{ \Carbon\Carbon::parse($bonCommande->date_commande)->format('d/m/Y') }}</td>
                                        </tr>
                                        @if($bonCommande->date_livraison_prevue)
                                            <tr>
                                                <td><strong>Date livraison prévue :</strong></td>
                                                <td>{{ \Carbon\Carbon::parse($bonCommande->date_livraison_prevue)->format('d/m/Y') }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td><strong>Statut :</strong></td>
                                            <td>
                                                @switch($bonCommande->statut)
                                                    @case('brouillon')
                                                        <span class="badge bg-secondary">Brouillon</span>
                                                    @break
                                                    @case('envoye')
                                                        <span class="badge bg-info">Envoyé</span>
                                                    @break
                                                    @case('valide')
                                                        <span class="badge bg-success">Validé</span>
                                                    @break
                                                    @case('en_preparation')
                                                        <span class="badge bg-warning">En préparation</span>
                                                    @break
                                                    @case('livre')
                                                        <span class="badge bg-success">Livre</span>
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

                        <!-- Montants -->
                        <div class="col-md-6">
                            <div class="card card-outline card-success">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        <i class="fa-solid fa-coins me-2"></i>
                                        Montants
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Montant HT :</strong></td>
                                            <td class="text-end">{{ number_format($bonCommande->montant_ht, 2, ',', ' ') }} FCFA</td>
                                        </tr>
                                        <tr>
                                            <td><strong>TVA ({{ $bonCommande->tva }}%) :</strong></td>
                                            <td class="text-end">{{ number_format($bonCommande->montant_ht * ($bonCommande->tva / 100), 2, ',', ' ') }} FCFA</td>
                                        </tr>
                                        <tr class="table-primary">
                                            <td><strong>Montant TTC :</strong></td>
                                            <td class="text-end"><strong>{{ number_format($bonCommande->montant_ttc, 2, ',', ' ') }} FCFA</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes et conditions -->
                    @if($bonCommande->notes || $bonCommande->conditions_livraison)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card card-outline card-info">
                                    <div class="card-header">
                                        <h4 class="card-title">
                                            <i class="fa-solid fa-sticky-note me-2"></i>
                                            Notes et Conditions
                                        </h4>
                                    </div>
                                    <div class="card-body">
                                        @if($bonCommande->notes)
                                            <div class="mb-3">
                                                <h6><i class="fa-solid fa-clipboard me-2"></i>Notes :</h6>
                                                <p class="mb-0">{{ nl2br($bonCommande->notes) }}</p>
                                            </div>
                                        @endif

                                        @if($bonCommande->conditions_livraison)
                                            <div>
                                                <h6><i class="fa-solid fa-shipping-fast me-2"></i>Conditions de livraison :</h6>
                                                <p class="mb-0">{{ nl2br($bonCommande->conditions_livraison) }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Lignes du bon de commande -->
                    @if($lignes && $lignes->count() > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card card-outline card-warning">
                                    <div class="card-header">
                                        <h4 class="card-title">
                                            <i class="fa-solid fa-list me-2"></i>
                                            Articles ({{ $lignes->count() }})
                                        </h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Référence</th>
                                                        <th>Désignation</th>
                                                        <th>Quantité</th>
                                                        <th>Prix unitaire HT</th>
                                                        <th>Total HT</th>
                                                        <th>Total TTC</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($lignes as $ligne)
                                                        <tr>
                                                            <td>{{ $ligne->reference_produit }}</td>
                                                            <td>{{ $ligne->designation }}</td>
                                                            <td class="text-center">{{ $ligne->quantite }}</td>
                                                            <td class="text-end">{{ number_format($ligne->prix_unitaire_ht, 2, ',', ' ') }} FCFA</td>
                                                            <td class="text-end">{{ number_format($ligne->montant_ht, 2, ',', ' ') }} FCFA</td>
                                                            <td class="text-end">{{ number_format($ligne->montant_ttc, 2, ',', ' ') }} FCFA</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
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
                                                {{ $bonCommande->created_by_name ?? 'Système' }}
                                            </p>
                                            <p class="mb-0">
                                                <strong>Date de création :</strong>
                                                {{ \Carbon\Carbon::parse($bonCommande->created_at)->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            @if($bonCommande->validated_by_name)
                                                <p class="mb-1">
                                                    <strong>Validé par :</strong>
                                                    {{ $bonCommande->validated_by_name }}
                                                </p>
                                            @endif
                                            <p class="mb-0">
                                                <strong>Dernière modification :</strong>
                                                {{ \Carbon\Carbon::parse($bonCommande->updated_at)->format('d/m/Y H:i') }}
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
                                    <a href="{{ route('commercial.bons-commande.index') }}" class="btn btn-secondary">
                                        <i class="fa-solid fa-arrow-left me-2"></i>
                                        Retour à la liste
                                    </a>
                                </div>
                                <div>
                                    <button onclick="window.print()" class="btn btn-outline-primary me-2">
                                        <i class="fa-solid fa-print me-2"></i>
                                        Imprimer
                                    </button>
                                    <a href="{{ route('commercial.bons-commande.edit', $bonCommande->id) }}" class="btn btn-warning">
                                        <i class="fa-solid fa-edit me-2"></i>
                                        Modifier
                                    </a>
                                    @if($bonCommande->statut == 'valide')
                                        <a href="{{ route('commercial.bons-livraison.create') }}?bon_commande_id={{ $bonCommande->id }}" class="btn btn-success">
                                            <i class="fa-solid fa-truck me-2"></i>
                                            Créer Bon de Livraison
                                        </a>
                                    @endif
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
