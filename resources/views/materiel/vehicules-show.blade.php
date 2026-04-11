@extends('layouts.app')

@section('title', 'Détails du Véhicule | KENAM SERVICES')

@section('content')
<div class="container-fluid py-4">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-truck me-2 text-primary"></i>Détails du Véhicule
            </h1>
            <p class="text-muted mb-0">{{ $vehicule->immatriculation }} - {{ $vehicule->marque }} {{ $vehicule->modele }}</p>
        </div>
        <div>
            <a href="{{ route('materiel.vehicules') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour à la liste
            </a>
            <a href="{{ route('materiel.vehicules.edit', $vehicule) }}" class="btn btn-primary">
                <i class="fas fa-edit me-1"></i>Modifier
            </a>
        </div>
    </div>

    <!-- Carte principale -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-info-circle me-2"></i>Informations générales
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Immatriculation:</strong></td>
                            <td>{{ $vehicule->immatriculation }}</td>
                        </tr>
                        <tr>
                            <td><strong>Marque:</strong></td>
                            <td>{{ $vehicule->marque }}</td>
                        </tr>
                        <tr>
                            <td><strong>Modèle:</strong></td>
                            <td>{{ $vehicule->modele }}</td>
                        </tr>
                        <tr>
                            <td><strong>Type:</strong></td>
                            <td>
                                <span class="badge bg-info">{{ $vehicule->type ?? 'Non spécifié' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Couleur:</strong></td>
                            <td>{{ $vehicule->couleur ?? 'Non spécifiée' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Année:</strong></td>
                            <td>{{ $vehicule->annee ?? 'Non spécifiée' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Statut:</strong></td>
                            <td>
                                @if($vehicule->statut == 'actif')
                                    <span class="badge bg-success">Actif</span>
                                @elseif($vehicule->statut == 'maintenance')
                                    <span class="badge bg-warning">En maintenance</span>
                                @elseif($vehicule->statut == 'hors_service')
                                    <span class="badge bg-danger">Hors service</span>
                                @else
                                    <span class="badge bg-secondary">{{ $vehicule->statut ?? 'Inconnu' }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Kilométrage:</strong></td>
                            <td>{{ number_format($vehicule->kilometrage ?? 0, 0, ' ', ' ') }} km</td>
                        </tr>
                        <tr>
                            <td><strong>Prix d'achat:</strong></td>
                            <td>{{ number_format($vehicule->prix_achat ?? 0, 0, ' ', ' ') }} FCFA</td>
                        </tr>
                        <tr>
                            <td><strong>Prix location/jour:</strong></td>
                            <td>{{ number_format($vehicule->prix_location ?? 0, 0, ' ', ' ') }} FCFA</td>
                        </tr>
                        <tr>
                            <td><strong>Date d'achat:</strong></td>
                            <td>{{ $vehicule->date_achat ? \Carbon\Carbon::parse($vehicule->date_achat)->format('d/m/Y') : 'Non spécifiée' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Carte grise:</strong></td>
                            <td>{{ $vehicule->carte_grise ?? 'Non spécifiée' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations supplémentaires -->
    @if($vehicule->description || $vehicule->observations)
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-comment me-2"></i>Informations supplémentaires
            </h5>
        </div>
        <div class="card-body">
            @if($vehicule->description)
            <div class="mb-3">
                <h6>Description:</h6>
                <p class="text-muted">{{ $vehicule->description }}</p>
            </div>
            @endif
            
            @if($vehicule->observations)
            <div>
                <h6>Observations:</h6>
                <p class="text-muted">{{ $vehicule->observations }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Actions rapides -->
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-tools me-2"></i>Actions rapides
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-2">
                    <a href="/materiel/maintenance/create?vehicule_id={{ $vehicule->id }}" class="btn btn-outline-primary w-100">
                        <i class="fas fa-wrench me-2"></i>Nouvelle maintenance
                    </a>
                </div>
                <div class="col-md-3 mb-2">
                    <a href="/materiel/visites/create?vehicle_id={{ $vehicule->id }}" class="btn btn-outline-success w-100">
                        <i class="fas fa-clipboard-check me-2"></i>Visite technique
                    </a>
                </div>
                <div class="col-md-3 mb-2">
                    <a href="/materiel/assurances/create?vehicle_id={{ $vehicule->id }}" class="btn btn-outline-warning w-100">
                        <i class="fas fa-shield-alt me-2"></i>Assurance
                    </a>
                </div>
                <div class="col-md-3 mb-2">
                    <a href="/materiel/carburant/create?vehicle_id={{ $vehicule->id }}" class="btn btn-outline-info w-100">
                        <i class="fas fa-gas-pump me-2"></i>Carburant
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire de suppression -->
    <div class="card shadow-sm mt-4 border-danger">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0">
                <i class="fas fa-trash me-2"></i>Zone de danger
            </h5>
        </div>
        <div class="card-body">
            <p class="text-muted mb-3">
                <i class="fas fa-exclamation-triangle me-2"></i>
                La suppression de ce véhicule est irréversible. Toutes les données associées seront perdues.
            </p>
            <form action="{{ route('materiel.vehicules.destroy', $vehicule) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce véhicule ? Cette action est irréversible.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash me-2"></i>Supprimer ce véhicule
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
