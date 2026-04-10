@extends('layouts.app')

@section('title', 'Devis - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-invoice me-2 text-warning"></i>Devis
            </h1>
            <p class="text-muted mb-0">Gestion des devis clients</p>
        </div>
        <div>
            <a href="{{ route('commercial.devis.create') }}" class="btn btn-warning">
                <i class="fas fa-plus me-2"></i>Nouveau Devis
            </a>
            <button class="btn btn-outline-secondary ms-2" onclick="window.location.reload()">
                <i class="fas fa-sync-alt me-1"></i> Actualiser
            </button>
        </div>
    </div>

    @if(!empty($dbError))
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ $dbError }}
        </div>
    @endif

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
                        <option value="accepte">Accepté</option>
                        <option value="rejete">Rejeté</option>
                        <option value="expire">Expiré</option>
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
                        <button class="btn btn-outline-warning">
                            <i class="fas fa-search me-1"></i> Rechercher
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des devis -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-warning">
                <i class="fas fa-list me-2"></i>Liste des Devis
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>N° Devis</th>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Montant</th>
                            <th>Validité</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($devis) && $devis->count() > 0)
                            @foreach($devis as $devi)
                            <tr>
                                <td class="fw-bold">{{ $devi->reference ?? 'DEV-' . date('Y') . '-' . str_pad($devi->id, 3, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ \Carbon\Carbon::parse($devi->issue_date ?? $devi->created_at)->format('d/m/Y') }}</td>
                                <td>{{ $devi->client_nom ?? 'Client ' . $devi->client_id }}</td>
                                <td class="text-success fw-bold">{{ number_format($devi->total_ttc ?? $devi->montant_ttc ?? $devi->montant_ht ?? 0, 0, ',', ' ') }} FCFA</td>
                                <td>{{ $devi->validite ? \Carbon\Carbon::parse($devi->validite)->format('d/m/Y') : 'Non définie' }}</td>
                                <td>
                                    @switch($devi->statut ?? 'en_attente')
                                        @case('en_attente')
                                            <span class="badge bg-warning">En attente</span>
                                            @break
                                        @case('accepte')
                                            <span class="badge bg-success">Accepté</span>
                                            @break
                                        @case('rejete')
                                            <span class="badge bg-danger">Rejeté</span>
                                            @break
                                        @case('expire')
                                            <span class="badge bg-secondary">Expiré</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $devi->statut ?? 'En attente' }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('commercial.devis.show', $devi->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(in_array($devi->statut ?? 'en_attente', ['en_attente', 'rejete', 'expire']))
                                            <a href="{{ route('commercial.devis.edit', $devi->id) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        @if($devi->statut === 'en_attente')
                                            <button class="btn btn-sm btn-outline-success" onclick="accepterDevis({{ $devi->id }})">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                        <button class="btn btn-sm btn-outline-info" onclick="genererPDF({{ $devi->id }})">
                                            <i class="fas fa-file-pdf"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Aucun devis trouvé</p>
                                    <a href="{{ route('commercial.devis.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Créer un devis
                                    </a>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(isset($devis) && method_exists($devis, 'links'))
                <div class="mt-3">
                    {{ $devis->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mt-4">
        @if(isset($stats))
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-start border-warning border-4 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small text-uppercase fw-bold">Devis en attente</div>
                                <div class="h3 mb-0 text-warning">{{ $stats['en_attente'] ?? 0 }}</div>
                                <small class="text-muted">En cours de validation</small>
                            </div>
                            <i class="fas fa-clock fa-3x text-warning opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-start border-success border-4 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small text-uppercase fw-bold">Devis acceptés</div>
                                <div class="h3 mb-0 text-success">{{ $stats['accepte'] ?? 0 }}</div>
                                <small class="text-muted">Validés</small>
                            </div>
                            <i class="fas fa-check-circle fa-3x text-success opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-start border-danger border-4 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small text-uppercase fw-bold">Devis rejetés</div>
                                <div class="h3 mb-0 text-danger">{{ $stats['rejete'] ?? 0 }}</div>
                                <small class="text-muted">Non validés</small>
                            </div>
                            <i class="fas fa-times-circle fa-3x text-danger opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-start border-info border-4 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small text-uppercase fw-bold">Taux conversion</div>
                                <div class="h3 mb-0 text-info">
                                    @if(isset($stats['total']) && $stats['total'] > 0)
                                        {{ round(($stats['accepte'] / $stats['total']) * 100, 1) }}%
                                    @else
                                        0%
                                    @endif
                                </div>
                                <small class="text-muted">Acceptés / Total</small>
                            </div>
                            <i class="fas fa-chart-line fa-3x text-info opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Les statistiques ne sont pas disponibles - Vérifiez la connexion à la base de données
                </div>
            </div>
        @endif
    </div>
</div>

@endsection
