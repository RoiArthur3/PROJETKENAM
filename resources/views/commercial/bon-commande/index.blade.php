@extends('layouts.app')

@section('title', 'Bons de Commande - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-contract me-2 text-primary"></i>Bons de Commande
            </h1>
            <p class="text-muted mb-0">Gestion des bons de commande clients</p>
        </div>
        <div>
            <a href="{{ route('commercial.bon-commande.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouveau Bon de Commande
            </a>
            <button class="btn btn-outline-secondary ms-2" onclick="window.location.reload()">
                <i class="fas fa-sync-alt me-1"></i> Actualiser
            </button>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">Recherche</label>
                    <input type="text" class="form-control" placeholder="N°, client, référence...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Statut</label>
                    <select class="form-select">
                        <option value="">Tous</option>
                        <option value="en_attente">En attente</option>
                        <option value="valide">Validé</option>
                        <option value="livre">Livré</option>
                        <option value="annule">Annulé</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date début</label>
                    <input type="date" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date fin</label>
                    <input type="date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button class="btn btn-outline-primary">
                            <i class="fas fa-search me-1"></i> Rechercher
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des bons de commande -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-list me-2"></i>Liste des Bons de Commande
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>N° Bon</th>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Durée</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $statusClasses = [
                                'en_attente' => 'bg-warning',
                                'valide' => 'bg-success',
                                'livre' => 'bg-info',
                                'annule' => 'bg-danger',
                            ];
                        @endphp
                        @forelse($bonsCommande as $bonCommande)
                            <tr>
                                <td class="fw-bold">{{ $bonCommande->reference }}</td>
                                <td>{{ $bonCommande->date_commande ? \Carbon\Carbon::parse($bonCommande->date_commande)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $bonCommande->client_nom ?? 'Client inconnu' }}</td>
                                <td>{{ (int) ($bonCommande->duration_days ?? 0) }} j</td>
                                <td class="text-success fw-bold">{{ number_format((float) ($bonCommande->montant_total ?? 0), 0, ',', ' ') }} FCFA</td>
                                <td>
                                    <span class="badge {{ $statusClasses[$bonCommande->statut] ?? 'bg-secondary' }}">{{ ucfirst(str_replace('_', ' ', $bonCommande->statut)) }}</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('materiel.missions.create', [
                                            'source_type' => 'bon_commande',
                                            'source_id' => $bonCommande->id,
                                            'source_reference' => $bonCommande->reference,
                                            'client_id' => $bonCommande->client_id,
                                            'start_at' => $bonCommande->date_commande,
                                            'end_at' => $bonCommande->date_livraison_prevue,
                                            'duration_days' => $bonCommande->duration_days,
                                        ]) }}" class="btn btn-sm btn-outline-info" title="Créer mission engin">
                                            <i class="fas fa-truck"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Aucun bon de commande disponible pour lancer une mission.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if(method_exists($bonsCommande, 'links'))
                <div class="mt-3">
                    {{ $bonsCommande->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
