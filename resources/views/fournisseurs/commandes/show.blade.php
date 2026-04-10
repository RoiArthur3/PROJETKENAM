@extends('layouts.app')

@section('title', 'Détails Commande Fournisseur - KENAM SERVICES')

@section('content')
<!-- Messages Flash -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold">
                <i class="fas fa-file-invoice text-success me-2"></i>Commande #{{ $commande->reference }}
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('fournisseurs.commandes.index') }}" class="text-decoration-none">Commandes</a></li>
                    <li class="breadcrumb-item active">{{ $commande->reference }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('fournisseurs.commandes.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
            @if(in_array($commande->statut, ['brouillon', 'en_attente']))
            <a href="#" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Modifier
            </a>
            @endif
            <button class="btn btn-outline-primary" onclick="window.print()">
                <i class="fas fa-print me-1"></i>Imprimer
            </button>
        </div>
    </div>

    <!-- KPIs -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0d6efd !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase text-muted small fw-bold mb-1">Montant HT</div>
                            <div class="h4 mb-0 fw-bold text-primary">{{ number_format($commande->montant_ht ?? 0, 0, ',', ' ') }}</div>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-coins fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #198754 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase text-muted small fw-bold mb-1">Montant TTC</div>
                            <div class="h4 mb-0 fw-bold text-success">{{ number_format($commande->montant_ttc ?? 0, 0, ',', ' ') }}</div>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-coins fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ffc107 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase text-muted small fw-bold mb-1">Statut</div>
                            <div class="h5 mb-0 fw-bold text-warning">
                                @php
                                    $statusConfig = [
                                        'brouillon' => ['bg' => 'secondary', 'icon' => 'fa-pencil-alt', 'label' => 'Brouillon'],
                                        'en_attente' => ['bg' => 'warning', 'icon' => 'fa-clock', 'label' => 'En attente'],
                                        'validee' => ['bg' => 'info', 'icon' => 'fa-check', 'label' => 'Validée'],
                                        'en_cours' => ['bg' => 'primary', 'icon' => 'fa-spinner', 'label' => 'En cours'],
                                        'livree' => ['bg' => 'success', 'icon' => 'fa-truck', 'label' => 'Livrée'],
                                        'partiellement_livree' => ['bg' => 'info', 'icon' => 'fa-truck-loading', 'label' => 'Partielle'],
                                        'annulee' => ['bg' => 'danger', 'icon' => 'fa-times', 'label' => 'Annulée'],
                                    ];
                                    $status = $statusConfig[$commande->statut] ?? $statusConfig['en_attente'];
                                @endphp
                                <span class="badge bg-{{ $status['bg'] }} bg-opacity-10 text-{{ $status['bg'] }} px-3 py-2">
                                    <i class="fas {{ $status['icon'] }} me-1"></i>{{ $status['label'] }}
                                </span>
                            </div>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-info-circle fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0dcaf0 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase text-muted small fw-bold mb-1">Date Livraison</div>
                            <div class="h5 mb-0 fw-bold text-info">
                                @if($commande->date_livraison_prevue)
                                    {{ \Carbon\Carbon::parse($commande->date_livraison_prevue)->format('d/m/Y') }}
                                @else
                                    Non définie
                                @endif
                            </div>
                        </div>
                        <div class="bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-truck fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations principales -->
    <div class="row">
        <div class="col-lg-8">
            <!-- Informations commande -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-gradient-success text-white">
                    <h6 class="m-0 font-weight-bold">Informations de la Commande</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">Référence</label>
                            <div class="fw-bold">{{ $commande->reference }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">Date de commande</label>
                            <div>{{ $commande->date_commande ? \Carbon\Carbon::parse($commande->date_commande)->format('d/m/Y') : '-' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">Date de livraison prévue</label>
                            <div>
                                @if($commande->date_livraison_prevue)
                                    {{ \Carbon\Carbon::parse($commande->date_livraison_prevue)->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">Mode de paiement</label>
                            <div>{{ $commande->mode_paiement ? ucfirst($commande->mode_paiement) : '-' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">Conditions de paiement</label>
                            <div>{{ $commande->conditions_paiement ?: '-' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">Frais de livraison</label>
                            <div>{{ number_format($commande->frais_livraison ?? 0, 0, ',', ' ') }} FCFA</div>
                        </div>
                        @if($commande->remise > 0)
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">Remise</label>
                            <div>{{ number_format($commande->remise, 0, ',', ' ') }} {{ $commande->type_remise == 'pourcentage' ? '%' : 'FCFA' }}</div>
                        </div>
                        @endif
                        @if($commande->notes)
                        <div class="col-12 mb-3">
                            <label class="form-label small fw-bold text-muted">Notes</label>
                            <div>{{ $commande->notes }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Articles de la commande -->
            <div class="card shadow">
                <div class="card-header py-3 bg-gradient-success text-white">
                    <h6 class="m-0 font-weight-bold">Articles de la Commande</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Article</th>
                                    <th class="text-center">Quantité</th>
                                    <th class="text-end">Prix Unitaire HT</th>
                                    <th class="text-end">Total HT</th>
                                    <th class="text-center">TVA</th>
                                    <th class="text-end">Total TTC</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($commande->lignes as $ligne)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $ligne->designation }}</div>
                                        @if($ligne->description)
                                            <div class="text-muted small">{{ $ligne->description }}</div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div>{{ number_format($ligne->quantite, 2, ',', ' ') }}</div>
                                        <div class="text-muted small">{{ $ligne->unite }}</div>
                                    </td>
                                    <td class="text-end">{{ number_format($ligne->prix_unitaire_ht, 0, ',', ' ') }}</td>
                                    <td class="text-end">{{ number_format($ligne->montant_ht, 0, ',', ' ') }}</td>
                                    <td class="text-center">{{ $ligne->tva_taux }}%</td>
                                    <td class="text-end fw-bold">{{ number_format($ligne->montant_ttc, 0, ',', ' ') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Aucun article dans cette commande</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="3" class="text-end">Total HT</th>
                                    <td class="text-end fw-bold">{{ number_format($commande->montant_ht, 0, ',', ' ') }}</td>
                                    <td class="text-center">{{ number_format($commande->tva, 0, ',', ' ') }}</td>
                                    <td class="text-end fw-bold">{{ number_format($commande->montant_ttc, 0, ',', ' ') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Informations fournisseur -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-gradient-success text-white">
                    <h6 class="m-0 font-weight-bold">Fournisseur</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-circle bg-success text-white me-3">
                            {{ strtoupper(substr($commande->fournisseur->raison_sociale ?? 'N/A', 0, 2)) }}
                        </div>
                        <div>
                            <div class="fw-bold">{{ $commande->fournisseur->raison_sociale ?? 'N/A' }}</div>
                            @if($commande->fournisseur->email)
                                <div class="text-muted small">{{ $commande->fournisseur->email }}</div>
                            @endif
                        </div>
                    </div>
                    @if($commande->fournisseur->telephone)
                    <div class="mb-2">
                        <i class="fas fa-phone text-muted me-2"></i>{{ $commande->fournisseur->telephone }}
                    </div>
                    @endif
                    @if($commande->fournisseur->adresse)
                    <div class="mb-2">
                        <i class="fas fa-map-marker-alt text-muted me-2"></i>{{ $commande->fournisseur->adresse }}
                        @if($commande->fournisseur->ville)
                            {{ $commande->fournisseur->ville }}
                            @if($commande->fournisseur->code_postal)
                                ({{ $commande->fournisseur->code_postal }})
                            @endif
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card shadow">
                <div class="card-header py-3 bg-gradient-success text-white">
                    <h6 class="m-0 font-weight-bold">Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(in_array($commande->statut, ['brouillon', 'en_attente']))
                        <a href="#" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Modifier la commande
                        </a>
                        <a href="#" class="btn btn-info">
                            <i class="fas fa-check me-2"></i>Valider la commande
                        </a>
                        @endif
                        @if($commande->statut == 'validee')
                        <a href="#" class="btn btn-primary">
                            <i class="fas fa-truck me-2"></i>Marquer en cours
                        </a>
                        @endif
                        @if(in_array($commande->statut, ['en_cours', 'partiellement_livree']))
                        <a href="#" class="btn btn-success">
                            <i class="fas fa-truck-loading me-2"></i>Réception partielle
                        </a>
                        <a href="#" class="btn btn-success">
                            <i class="fas fa-check-circle me-2"></i>Réception complète
                        </a>
                        @endif
                        <a href="#" class="btn btn-outline-primary">
                            <i class="fas fa-file-pdf me-2"></i>Télécharger PDF
                        </a>
                        <a href="#" class="btn btn-outline-success">
                            <i class="fas fa-envelope me-2"></i>Envoyer par email
                        </a>
                        @if(in_array($commande->statut, ['brouillon', 'en_attente']))
                        <hr class="my-2">
                        <a href="#" class="btn btn-outline-danger" onclick="return confirm('Supprimer cette commande ? Cette action est irréversible.');">
                            <i class="fas fa-trash me-2"></i>Supprimer
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}
</style>
@endsection
