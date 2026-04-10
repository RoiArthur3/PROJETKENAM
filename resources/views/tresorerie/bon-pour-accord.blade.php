@extends('layouts.app')

@section('title', 'Bons pour Accord - Tresorerie - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-check-double me-2 text-success"></i>Bons pour Accord
            </h1>
            <p class="text-muted mb-0">Documents et opérations en attente de validation définitive</p>
        </div>
        <div>
            <a href="{{ route('tresorerie.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour au Dashboard
            </a>
        </div>
    </div>

    <!-- Onglets -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="operations-tab" data-bs-toggle="tab" data-bs-target="#operations" type="button" role="tab" aria-controls="operations" aria-selected="true">
                <i class="fas fa-file-invoice me-1"></i>Opérations ({{ $bonsPourAccordOperations->count() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="factures-tab" data-bs-toggle="tab" data-bs-target="#factures" type="button" role="tab" aria-controls="factures" aria-selected="false">
                <i class="fas fa-file-contract me-1"></i>Factures Fournisseur ({{ $bonsPourAccordFactures->count() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="decaissements-tab" data-bs-toggle="tab" data-bs-target="#decaissements" type="button" role="tab" aria-controls="decaissements" aria-selected="false">
                <i class="fas fa-money-bill me-1"></i>Décaissements ({{ $bonsPourAccordDecaissements->count() }})
            </button>
        </li>
    </ul>

    <!-- Contenu des onglets -->
    <div class="tab-content">
        <!-- Onglet Opérations -->
        <div class="tab-pane fade show active" id="operations" role="tabpanel" aria-labelledby="operations-tab">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="m-0 fw-bold">
                        <i class="fas fa-file-invoice me-2"></i>Opérations en attente de validation
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if($bonsPourAccordOperations->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-check-circle fa-2x mb-2 d-block opacity-25"></i>
                            Aucune opération en attente de validation
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Référence</th>
                                        <th>Titre</th>
                                        <th>Montant</th>
                                        <th>Demandeur</th>
                                        <th>Date Création</th>
                                        <th>Étape Actuelle</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bonsPourAccordOperations as $op)
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary">{{ $op->numero_ordre ?? '#OP-'.$op->id }}</span>
                                            </td>
                                            <td>
                                                <strong>{{ \Illuminate\Support\Str::limit($op->titre, 40) }}</strong>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-success">{{ number_format($op->montant, 0, ',', ' ') }} FCFA</span>
                                            </td>
                                            <td>{{ $op->demandeur_name ?? 'N/A' }}</td>
                                            <td>{{ $op->created_at?->format('d/m/Y') }}</td>
                                            <td>
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-hourglass-half me-1"></i>
                                                    {{ getOperationStatusText($op->statut_courant) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('operations.show', $op->id) }}" class="btn btn-outline-primary" title="Consulter">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($op->demandeur_email !== auth()->user()->email)
                                                        <a href="{{ route('operations.validate', $op->id) }}" class="btn btn-outline-success" title="Valider">
                                                            <i class="fas fa-check"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Onglet Factures Fournisseur -->
        <div class="tab-pane fade" id="factures" role="tabpanel" aria-labelledby="factures-tab">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0 fw-bold">
                        <i class="fas fa-file-contract me-2"></i>Factures fournisseur en validation
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if($bonsPourAccordFactures->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-check-circle fa-2x mb-2 d-block opacity-25"></i>
                            Aucune facture fournisseur en attente de validation
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>N° Facture</th>
                                        <th>Fournisseur</th>
                                        <th>Montant TTC</th>
                                        <th>Montant Restant</th>
                                        <th>Date Facture</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bonsPourAccordFactures as $facture)
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary">{{ $facture->numero_facture }}</span>
                                            </td>
                                            <td>{{ $facture->fournisseur->nom ?? $facture->fournisseur_nom ?? 'N/A' }}</td>
                                            <td>
                                                <span class="fw-bold">{{ number_format($facture->montant_ttc, 0, ',', ' ') }} FCFA</span>
                                            </td>
                                            <td>
                                                <span class="text-warning fw-bold">{{ number_format($facture->montant_restant ?? $facture->montant_ttc, 0, ',', ' ') }} FCFA</span>
                                            </td>
                                            <td>{{ $facture->date_facturation?->format('d/m/Y') }}</td>
                                            <td>
                                                <span class="badge bg-warning text-dark">
                                                    {{ ucfirst($facture->statut) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    @if(method_exists($facture, 'route_show'))
                                                        <a href="{{ $facture->route_show() }}" class="btn btn-outline-primary" title="Consulter">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('tresorerie.decaissements.create', ['facture_id' => $facture->id]) }}" class="btn btn-outline-success" title="Créer décaissement">
                                                        <i class="fas fa-coin"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Onglet Décaissements -->
        <div class="tab-pane fade" id="decaissements" role="tabpanel" aria-labelledby="decaissements-tab">
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h6 class="m-0 fw-bold">
                        <i class="fas fa-money-bill me-2"></i>Décaissements en validation
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if($bonsPourAccordDecaissements->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-check-circle fa-2x mb-2 d-block opacity-25"></i>
                            Aucun décaissement en attente de validation
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Référence</th>
                                        <th>Date</th>
                                        <th>Libellé</th>
                                        <th>Bénéficiaire</th>
                                        <th>Montant</th>
                                        <th>Caisse</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bonsPourAccordDecaissements as $dec)
                                        <tr>
                                            <td>
                                                <span class="badge bg-warning text-dark">{{ $dec->reference }}</span>
                                            </td>
                                            <td>{{ $dec->date_depense?->format('d/m/Y') }}</td>
                                            <td>{{ \Illuminate\Support\Str::limit($dec->libelle, 30) }}</td>
                                            <td>{{ $dec->beneficiaire->name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="fw-bold text-danger">{{ number_format($dec->montant, 0, ',', ' ') }} FCFA</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $dec->caisse->nom ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $dec->statut === 'validé' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($dec->statut) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('tresorerie.decaissements.show', $dec->id) }}" class="btn btn-outline-primary" title="Consulter">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('tresorerie.decaissements.edit', $dec->id) }}" class="btn btn-outline-warning" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
