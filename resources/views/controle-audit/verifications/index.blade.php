@extends('layouts.app')

@section('title', 'Vérifications - Contrôle & Audit')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-check-circle text-primary me-2"></i>Vérifications d'Audit
            </h1>
            <p class="text-muted mb-0">Gestion des vérifications et contrôles d'audit</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('controle-audit.verifs.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouvelle vérification
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
                                Total vérifications
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $verifications->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                Validées
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $verifications->where('statut', 'validee')->count() }}</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $verifications->where('statut', 'en_cours')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-spinner fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Rejetées
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $verifications->where('statut', 'rejetee')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table des vérifications -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Liste des vérifications
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Référence</th>
                            <th>Titre</th>
                            <th>Type de vérification</th>
                            <th>Date</th>
                            <th>Responsable</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($verifications as $verification)
                            <tr>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">#{{ str_pad($verification->id, 4, '0', STR_PAD_LEFT) }}</code>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $verification->titre }}</div>
                                    <small class="text-muted">{{ $verification->description }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $verification->type_verification }}</span>
                                </td>
                                <td>
                                    {{ $verification->date_verification ? $verification->date_verification->format('d/m/Y') : 'N/A' }}
                                </td>
                                <td>
                                    @if($verification->responsable)
                                        {{ $verification->responsable->name }}
                                    @else
                                        <span class="text-muted">Non assigné</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $verification->statut === 'validee' ? 'success' : ($verification->statut === 'en_cours' ? 'warning' : ($verification->statut === 'rejetee' ? 'danger' : 'secondary')) }}">
                                        {{ ucfirst(str_replace('_', ' ', $verification->statut)) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('controle-audit.verifs.show', $verification) }}" class="btn btn-outline-primary" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('controle-audit.verifs.edit', $verification) }}" class="btn btn-outline-secondary" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-check-circle fa-3x text-muted mb-3"></i>
                                    <div class="text-muted">Aucune vérification trouvée</div>
                                    <p class="text-muted small">Commencez par créer votre première vérification d'audit.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $verifications->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
