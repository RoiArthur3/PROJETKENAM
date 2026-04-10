@extends('layouts.app')

@section('title', 'Dashboard Requêtes - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-clipboard-list me-2"></i>
                Dashboard Requêtes
            </h1>
            <p class="text-muted mb-0">Vue synthèse de vos requêtes d'opération</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('agent.requetes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Nouvelle requête
            </a>
            <a href="{{ route('agent.requetes.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list me-1"></i>Voir la liste
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-clipboard-list fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">En attente</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['en_attente'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-clock fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">En cours</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['en_cours'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-spinner fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Clôturées</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['cloturees'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history me-2"></i>Dernières requêtes
                    </h6>
                    <a href="{{ route('agent.requetes.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-list me-1"></i>Tout voir
                    </a>
                </div>
                <div class="card-body">
                    @if(($dernieresRequetes ?? collect())->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Référence</th>
                                        <th>Objet</th>
                                        <th>Statut</th>
                                        <th>Date</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dernieresRequetes as $r)
                                        <tr>
                                            <td>{{ $r->reference }}</td>
                                            <td>{{ $r->objet }}</td>
                                            <td><span class="badge bg-secondary">{{ $r->statut_label }}</span></td>
                                            <td>{{ $r->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('agent.requetes.show', $r) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                            <div class="text-muted">Aucune requête pour le moment.</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt me-2"></i>Actions rapides
                    </h6>
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="{{ route('agent.requetes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Nouvelle requête
                    </a>
                    <a href="{{ route('agent.requetes.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-list me-1"></i>Mes requêtes
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
