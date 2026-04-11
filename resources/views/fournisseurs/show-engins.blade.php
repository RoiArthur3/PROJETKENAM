@extends('layouts.app')

@section('title', 'Fournisseur d\'Engins - Détail | KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-8">
                    <h1 class="m-0 d-flex align-items-center gap-2 flex-wrap">
                        <span class="avatar-initials bg-success text-white">
                            <i class="fas fa-truck"></i>
                        </span>
                        {{ $fournisseur->raison_sociale ?? 'Fournisseur d\'Engins' }}
                        <span class="badge bg-{{ $fournisseur->est_actif ? 'success' : 'secondary' }} fs-6 align-middle">
                            {{ $fournisseur->est_actif ? 'Actif' : 'Inactif' }}
                        </span>
                        <span class="badge bg-info fs-6 align-middle">
                            <i class="fas fa-truck me-1"></i>Fournisseur d'Engins
                        </span>
                    </h1>
                    <p class="mb-0 text-muted mt-1 small">
                        @if($fournisseur->categorie ?? null)
                            <span class="me-3"><i class="fas fa-tag me-1"></i>{{ $fournisseur->categorie->nom }}</span>
                        @endif
                        @if($fournisseur->ville ?? null)
                            <span class="me-3"><i class="fas fa-map-marker-alt me-1"></i>{{ $fournisseur->ville }}</span>
                        @endif
                        @if($fournisseur->telephone ?? null)
                            <span><i class="fas fa-phone me-1"></i>{{ $fournisseur->telephone }}</span>
                        @endif
                    </p>
                </div>
                <div class="col-sm-4">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('fournisseurs.engins.kenam') }}">Engins Kenam</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $fournisseur->raison_sociale ?? 'Détails' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- KPIs Fournisseur -->
            <div class="row mb-4">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $totalEngins ?? 0 }}</h3>
                            <p>Engins Associés</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-truck"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ number_format($totalFactures ?? 0, 0, ',', ' ') }} FCFA</h3>
                            <p>Total Factures</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ number_format($totalRegle ?? 0, 0, ',', ' ') }} FCFA</h3>
                            <p>Montant Réglé</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ number_format($soldeRestant ?? 0, 0, ',', ' ') }} FCFA</h3>
                            <p>Solde Restant</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations principales -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-building me-2"></i>
                                Informations du Fournisseur
                            </h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Raison Sociale:</th>
                                    <td>{{ $fournisseur->raison_sociale ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Forme Juridique:</th>
                                    <td>{{ $fournisseur->forme_juridique ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>SIRET:</th>
                                    <td>{{ $fournisseur->siret ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>TVA Intracom:</th>
                                    <td>{{ $fournisseur->tva_intracom ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ $fournisseur->email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Téléphone:</th>
                                    <td>{{ $fournisseur->telephone ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Adresse:</th>
                                    <td>
                                        {{ $fournisseur->adresse ?? 'N/A' }}<br>
                                        {{ $fournisseur->code_postal ?? '' }} {{ $fournisseur->ville ?? '' }}<br>
                                        {{ $fournisseur->pays ?? 'N/A' }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-euro-sign me-2"></i>
                                Conditions Financières
                            </h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Mode de Règlement:</th>
                                    <td>{{ $fournisseur->mode_reglement ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Conditions:</th>
                                    <td>{{ $fournisseur->condition_reglement ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Délai Livraison:</th>
                                    <td>{{ $fournisseur->delai_livraison ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Code Comptable:</th>
                                    <td>{{ $fournisseur->code_comptable ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Chiffre d'Affaires:</th>
                                    <td>{{ number_format($fournisseur->chiffre_affaires ?? 0, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr>
                                    <th>Nombre Commandes:</th>
                                    <td>{{ $fournisseur->nombre_commandes ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <th>Évaluation Moyenne:</th>
                                    <td>
                                        @if($fournisseur->evaluation_moyenne)
                                            <div class="rating">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $fournisseur->evaluation_moyenne ? 'text-warning' : 'text-muted' }}"></i>
                                                @endfor
                                                <span class="ms-2">{{ $fournisseur->evaluation_moyenne }}/5</span>
                                            </div>
                                        @else
                                            Non évalué
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Engins associés -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-truck me-2"></i>
                                Engins Associés
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addEnginModal">
                                    <i class="fas fa-plus"></i> Ajouter un engin
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Immatriculation</th>
                                            <th>Marque/Modèle</th>
                                            <th>Type</th>
                                            <th>Tarif Journalier</th>
                                            <th>Statut</th>
                                            <th>Dernière Mission</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse(($engins ?? []) as $engin)
                                            <tr>
                                                <td><strong>{{ $engin->immatriculation ?? 'N/A' }}</strong></td>
                                                <td>{{ $engin->marque }} {{ $engin->modele }}</td>
                                                <td>{{ $engin->type_materiel }}</td>
                                                <td>{{ number_format($engin->tarif_journalier ?? 0, 0, ',', ' ') }} FCFA</td>
                                                <td>
                                                    <span class="badge bg-{{ $engin->disponible ? 'success' : 'warning' }}">
                                                        {{ $engin->disponible ? 'Disponible' : 'En mission' }}
                                                    </span>
                                                </td>
                                                <td>{{ $engin->derniere_mission ?? 'N/A' }}</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button class="btn btn-outline-warning">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">
                                                    <i class="fas fa-truck fa-2x mb-2"></i>
                                                    <p>Aucun engin associé à ce fournisseur</p>
                                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addEnginModal">
                                                        <i class="fas fa-plus"></i> Ajouter le premier engin
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historique des paiements -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-money-bill-wave me-2"></i>
                                Historique des Paiements
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addPaiementModal">
                                    <i class="fas fa-plus"></i> Enregistrer un paiement
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Date</th>
                                            <th>Référence</th>
                                            <th>Type</th>
                                            <th>Montant</th>
                                            <th>Statut</th>
                                            <th>Mode</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse(($paiements ?? []) as $paiement)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') }}</td>
                                                <td><strong>{{ $paiement->reference }}</strong></td>
                                                <td>
                                                    <span class="badge bg-info">{{ $paiement->type }}</span>
                                                </td>
                                                <td>{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</td>
                                                <td>
                                                    <span class="badge bg-{{ $paiement->statut == 'paye' ? 'success' : 'warning' }}">
                                                        {{ $paiement->statut == 'paye' ? 'Payé' : 'En attente' }}
                                                    </span>
                                                </td>
                                                <td>{{ $paiement->mode_paiement }}</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-primary">
                                                            <i class="fas fa-receipt"></i>
                                                        </button>
                                                        <button class="btn btn-outline-success">
                                                            <i class="fas fa-download"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">
                                                    <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                                                    <p>Aucun paiement enregistré</p>
                                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addPaiementModal">
                                                        <i class="fas fa-plus"></i> Enregistrer le premier paiement
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <a href="{{ route('fournisseurs.engins.kenam') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Retour aux Engins Kenam
                                    </a>
                                    <a href="{{ route('fournisseurs.edit', $fournisseur->id) }}" class="btn btn-warning">
                                        <i class="fas fa-edit me-2"></i>Modifier
                                    </a>
                                </div>
                                <div>
                                    <button class="btn btn-info">
                                        <i class="fas fa-file-pdf me-2"></i>Exporter PDF
                                    </button>
                                    <button class="btn btn-primary">
                                        <i class="fas fa-envelope me-2"></i>Envoyer Email
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Ajout Engin -->
<div class="modal fade" id="addEnginModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un engin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Immatriculation</label>
                        <input type="text" class="form-control" placeholder="ABC-123">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Marque/Modèle</label>
                        <input type="text" class="form-control" placeholder="Volvo FH16">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select class="form-select">
                            <option>Camion</option>
                            <option>Engin</option>
                            <option>Véhicule léger</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tarif Journalier</label>
                        <input type="number" class="form-control" placeholder="50000">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary">Ajouter</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajout Paiement -->
<div class="modal fade" id="addPaiementModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Enregistrer un paiement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Date de paiement</label>
                        <input type="date" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type de paiement</label>
                        <select class="form-select">
                            <option>Avance</option>
                            <option>Solde</option>
                            <option>Acompte</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Montant</label>
                        <input type="number" class="form-control" placeholder="50000">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mode de paiement</label>
                        <select class="form-select">
                            <option>Virement bancaire</option>
                            <option>Chèque</option>
                            <option>Espèces</option>
                            <option>Mobile Money</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Référence</label>
                        <input type="text" class="form-control" placeholder="REF-001">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-success">Enregistrer</button>
            </div>
        </div>
    </div>
</div>
@endsection
