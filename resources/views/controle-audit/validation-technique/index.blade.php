@extends('layouts.app')

@section('title', 'Validation Technique - Contrôle & Audit')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-cogs text-primary me-2"></i>Validation Technique
            </h1>
            <p class="text-muted mb-0">Gestion des validations techniques et conformités</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('controle-audit.validation-tech.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouvelle validation technique
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
                                Total validations
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $validationsTech->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cogs fa-2x text-gray-300"></i>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $validationsTech->where('statut', 'validee')->count() }}</div>
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
                                En attente
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $validationsTech->where('statut', 'en_attente')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $validationsTech->where('statut', 'rejetee')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table des validations techniques -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Liste des validations techniques
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Référence</th>
                            <th>Objet</th>
                            <th>Type de validation</th>
                            <th>Date</th>
                            <th>Technicien</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($validationsTech as $validation)
                            <tr>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">#{{ str_pad($validation->id, 4, '0', STR_PAD_LEFT) }}</code>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $validation->objet }}</div>
                                    <small class="text-muted">{{ $validation->description }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $validation->type_validation }}</span>
                                </td>
                                <td>
                                    {{ $validation->date_validation ? $validation->date_validation->format('d/m/Y') : 'N/A' }}
                                </td>
                                <td>
                                    @if($validation->technicien)
                                        {{ $validation->technicien->name }}
                                    @else
                                        <span class="text-muted">Non assigné</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $validation->statut === 'validee' ? 'success' : ($validation->statut === 'en_attente' ? 'warning' : ($validation->statut === 'rejetee' ? 'danger' : 'secondary')) }}">
                                        {{ ucfirst(str_replace('_', ' ', $validation->statut)) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('controle-audit.validation-tech.show', $validation) }}" class="btn btn-outline-primary" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('controle-audit.validation-tech.edit', $validation) }}" class="btn btn-outline-secondary" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-cogs fa-3x text-muted mb-3"></i>
                                    <div class="text-muted">Aucune validation technique trouvée</div>
                                    <p class="text-muted small">Commencez par créer votre première validation technique.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $validationsTech->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
