<x-dashboard-layout title="Détails de la Commande" icon="fa-solid fa-clipboard-list">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fa-solid fa-clipboard-list me-2"></i>
                        Commande #{{ $commande->reference_commande }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('commande.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fa-solid fa-arrow-left me-1"></i>
                            Retour à la liste
                        </a>
                        <a href="{{ route('commande.edit', $commande->id) }}" class="btn btn-warning btn-sm">
                            <i class="fa-solid fa-edit me-1"></i>
                            Modifier
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Informations principales -->
                        <div class="col-md-6">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fa-solid fa-info-circle me-2"></i>
                                        Informations
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Référence:</strong></td>
                                            <td><span class="badge bg-primary">{{ $commande->reference_commande }}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Type:</strong></td>
                                            <td>{{ $commande->type_commande }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Statut:</strong></td>
                                            <td>
                                                @switch($commande->statut)
                                                    @case('en_attente')
                                                        <span class="badge bg-warning">En attente</span>
                                                    @break
                                                    @case('en_cours')
                                                        <span class="badge bg-info">En cours</span>
                                                    @break
                                                    @case('termine')
                                                        <span class="badge bg-success">Terminée</span>
                                                    @break
                                                    @case('annule')
                                                        <span class="badge bg-danger">Annulée</span>
                                                    @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ $commande->statut }}</span>
                                                @endswitch
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Date commande:</strong></td>
                                            <td>{{ \Carbon\Carbon::parse($commande->date_commande)->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Date début:</strong></td>
                                            <td>{{ $commande->date_debut ? \Carbon\Carbon::parse($commande->date_debut)->format('d/m/Y H:i') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Date fin:</strong></td>
                                            <td>{{ $commande->date_fin ? \Carbon\Carbon::parse($commande->date_fin)->format('d/m/Y H:i') : '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Chauffeur et Véhicule -->
                        <div class="col-md-6">
                            <div class="card card-outline card-info">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fa-solid fa-users me-2"></i>
                                        Affectation
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Chauffeur:</strong></td>
                                            <td>
                                                @if($commande->chauffeur_nom)
                                                    <span class="badge bg-info">
                                                        {{ $commande->chauffeur_prenom }} {{ $commande->chauffeur_nom }}
                                                    </span>
                                                    <br>
                                                    <small class="text-muted">{{ $commande->chauffeur_telephone }}</small>
                                                @else
                                                    <span class="text-muted">Non assigné</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Véhicule:</strong></td>
                                            <td>
                                                @if($commande->immatriculation)
                                                    <span class="badge bg-secondary">{{ $commande->immatriculation }}</span>
                                                    <br>
                                                    <small class="text-muted">{{ $commande->marque }} {{ $commande->modele }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Client:</strong></td>
                                            <td>
                                                @if($commande->client_nom)
                                                    <span class="badge bg-success">{{ $commande->client_nom }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Lieux et Kilométrage -->
                        <div class="col-12">
                            <div class="card card-outline card-warning">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fa-solid fa-map-marker-alt me-2"></i>
                                        Détails de la Mission
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6><strong>Lieux</strong></h6>
                                            <table class="table table-sm">
                                                <tr>
                                                    <td><strong>Départ:</strong></td>
                                                    <td>{{ $commande->lieu_depart }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Arrivée:</strong></td>
                                                    <td>{{ $commande->lieu_arrivee ?: '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Retour:</strong></td>
                                                    <td>{{ $commande->lieu_retour ?: '-' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <h6><strong>Kilométrage</strong></h6>
                                            <table class="table table-sm">
                                                <tr>
                                                    <td><strong>Départ:</strong></td>
                                                    <td>{{ number_format($commande->kilometrage_depart, 2) }} km</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Retour:</strong></td>
                                                    <td>{{ $commande->kilometrage_retour ? number_format($commande->kilometrage_retour, 2) . ' km' : '-' }}</td>
                                                </tr>
                                                @if($commande->kilometrage_depart && $commande->kilometrage_retour)
                                                    <tr>
                                                        <td><strong>Distance:</strong></td>
                                                        <td>{{ number_format($commande->kilometrage_retour - $commande->kilometrage_depart, 2) }} km</td>
                                                    </tr>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description et Observations -->
                        <div class="col-12">
                            <div class="card card-outline card-secondary">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fa-solid fa-comment me-2"></i>
                                        Notes et Observations
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6><strong>Description</strong></h6>
                                            <p>{{ $commande->description ?: 'Aucune description' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6><strong>Observations</strong></h6>
                                            <p>{{ $commande->observations ?: 'Aucune observation' }}</p>
                                        </div>
                                    </div>
                                    @if($commande->notes)
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <h6><strong>Notes supplémentaires</strong></h6>
                                                <p>{{ $commande->notes }}</p>
                                            </div>
                                        </div>
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
