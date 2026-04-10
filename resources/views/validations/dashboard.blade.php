@extends('layouts.app')

@section('title', 'Dashboard Validations - Suivi & Validation')

@section('content')
<div class="container-fluid">
    @php
        // Some routes pass $validations, others pass $recent_validations.
        $validationsSource = $validations ?? ($recent_validations ?? collect());

        if ($validationsSource instanceof \Illuminate\Pagination\AbstractPaginator) {
            $validations = $validationsSource->getCollection();
        } elseif ($validationsSource instanceof \Illuminate\Support\Collection) {
            $validations = $validationsSource;
        } else {
            $validations = collect($validationsSource);
        }
    @endphp

    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-tasks text-primary me-2"></i>Dashboard Validations
                    </h1>
                    <p class="text-muted mb-0">Suivi des demandes et validations en cours par service</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques générales -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Validations en attente
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $validations->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Services concernés
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $validations->pluck('initiateur.service.nom')->unique()->count() ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Validées aujourd'hui
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    try {
                                        echo \App\Models\Validation::where('statut', 'validee')->whereDate('updated_at', today())->count();
                                    } catch (\Exception $e) {
                                        echo '0';
                                    }
                                @endphp
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Taux de conversion
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    try {
                                        $total = $validations->count();
                                        $validees = $validations->where('statut', 'validee')->count();
                                        echo $total > 0 ? round(($validees / $total) * 100, 1) : 0;
                                    } catch (\Exception $e) {
                                        echo '0';
                                    }
                                @endphp
%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Validations par service -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-building me-2"></i>Validations en attente par service
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @php
                            $validationsByService = $validations->groupBy(function($validation) {
                                return $validation->initiateur->service->nom ?? 'Service non assigné';
                            });
                        @endphp

                        @forelse($validationsByService as $serviceName => $serviceValidations)
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                <div class="card border-left-primary shadow h-100">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    {{ $serviceName }}
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $serviceValidations->count() }}</div>
                                                <div class="text-xs text-muted">validation(s) en attente</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-users fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-light">
                                        <a href="#service-{{ \Illuminate\Support\Str::slug($serviceName) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse">
                                            Voir détails <i class="fas fa-chevron-down ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Aucune validation en attente pour le moment.
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Détails des validations par service -->
    @foreach($validationsByService as $serviceName => $serviceValidations)
        <div class="row mb-4 collapse" id="service-{{ \Illuminate\Support\Str::slug($serviceName) }}">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header py-3 bg-light">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-list me-2"></i>Détails - {{ $serviceName }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Initiateur</th>
                                        <th>Type de demande</th>
                                        <th>Description</th>
                                        <th>Date de demande</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($serviceValidations as $validation)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">{{ $validation->initiateur->name ?? 'N/A' }}</div>
                                                        <small class="text-muted">{{ $validation->initiateur->email ?? '' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $validation->type_demande ?? 'Général' }}</span>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 200px;" title="{{ $validation->description ?? 'Aucune description' }}">
                                                    {{ $validation->description ?? 'Aucune description' }}
                                                </div>
                                            </td>
                                            <td>{{ $validation->created_at ? $validation->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock me-1"></i>En attente
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-info btn-sm" onclick="viewValidation({{ $validation->id }})">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    @if(auth()->user() && ($validation->valideur_id == auth()->id() || auth()->user()->hasRole('admin')))
                                                        <button class="btn btn-outline-success btn-sm" onclick="approveValidation({{ $validation->id }})">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button class="btn btn-outline-danger btn-sm" onclick="rejectValidation({{ $validation->id }})">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<script>
function viewValidation(id) {
    window.location.href = `/validations/${id}`;
}

function approveValidation(id) {
    if (confirm('Êtes-vous sûr de vouloir approuver cette validation ?')) {
        // Implement approval logic
        console.log('Approve validation:', id);
    }
}

function rejectValidation(id) {
    const reason = prompt('Motif du rejet :');
    if (reason) {
        // Implement rejection logic
        console.log('Reject validation:', id, reason);
    }
}
</script>
@endsection
