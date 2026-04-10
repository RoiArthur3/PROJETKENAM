@extends('layouts.app')

@section('title', 'Rapports - Contrôle & Audit')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-alt text-primary me-2"></i>Rapports d'Audit
            </h1>
            <p class="text-muted mb-0">Gestion des rapports de contrôle et audit</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('controle-audit.rapports.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouveau rapport
            </a>
        </div>
    </div>

    <!-- KPIs -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total rapports
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $rapports->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Finalisés
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $rapports->where('statut', 'finalise')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                En cours
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $rapports->where('statut', 'en_cours')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-spinner fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Approuvés
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $rapports->where('statut', 'approuve')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-thumbs-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table des rapports -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Liste des rapports
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Référence</th>
                            <th>Titre</th>
                            <th>Type de rapport</th>
                            <th>Période</th>
                            <th>Auteur</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rapports as $rapport)
                            <tr>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">#{{ str_pad($rapport->id, 4, '0', STR_PAD_LEFT) }}</code>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $rapport->titre }}</div>
                                    <small class="text-muted">{{ $rapport->description }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $rapport->type_rapport }}</span>
                                </td>
                                <td>
                                    {{ $rapport->date_debut ? $rapport->date_debut->format('d/m/Y') : 'N/A' }} -
                                    {{ $rapport->date_fin ? $rapport->date_fin->format('d/m/Y') : 'N/A' }}
                                </td>
                                <td>
                                    @if($rapport->auteur)
                                        {{ $rapport->auteur->name }}
                                    @else
                                        <span class="text-muted">Non assigné</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $rapport->statut === 'finalise' ? 'success' : ($rapport->statut === 'en_cours' ? 'warning' : ($rapport->statut === 'approuve' ? 'info' : 'secondary')) }}">
                                        {{ ucfirst(str_replace('_', ' ', $rapport->statut)) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('controle-audit.rapports.show', $rapport) }}" class="btn btn-outline-primary" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('controle-audit.rapports.edit', $rapport) }}" class="btn btn-outline-secondary" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($rapport->fichier)
                                            <a href="{{ asset('storage/' . $rapport->fichier) }}" class="btn btn-outline-info" title="Télécharger" target="_blank">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                    <div class="text-muted">Aucun rapport trouvé</div>
                                    <p class="text-muted small">Commencez par créer votre premier rapport d'audit.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $rapports->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
