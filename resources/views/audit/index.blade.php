@extends('layouts.app')

@section('title', 'Contrôles - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Liste des Contrôles</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('audit.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
            <a href="{{ route('audit.controles') }}" class="btn btn-outline-primary active">
                <i class="fas fa-clipboard-check me-2"></i>Contrôles
            </a>
            <a href="{{ route('audit.rapports') }}" class="btn btn-outline-info">
                <i class="fas fa-file-alt me-2"></i>Rapports
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <h5 class="card-title mb-4">Historique des Audits</h5>

            <!-- Filtres (Similaire à Caisses) -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" placeholder="Rechercher un audit..." id="searchInput">
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="typeFilter">
                        <option value="">Tous les types</option>
                        <option value="interne">Interne</option>
                        <option value="externe">Externe</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-info">
                        <i class="fas fa-download me-2"></i>Exporter
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Titre</th>
                            <th>Auditeur</th>
                            <th>Date</th>
                            <th>Score</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($audits as $audit)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary text-white me-2" style="width: 30px; height: 30px; display:flex; justify-content:center; align-items:center; border-radius:50%;">
                                        <i class="fas fa-search" style="font-size: 12px;"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $audit->reference }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $audit->checking->title ?? 'Inspection #' . $audit->id }}</td>
                            <td>{{ $audit->inspector->name ?? 'Non assigné' }}</td>
                            <td>{{ $audit->created_at->format('d/m/Y') }}</td>
                            <td>{{ $audit->inspection_score !== null ? $audit->inspection_score . '%' : '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $audit->status === 'completed' ? 'success' : ($audit->status === 'in_progress' ? 'info' : 'secondary') }}">
                                    {{ $audit->formatted_status }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="#" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">Aucun audit enregistré</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                @if(method_exists($audits, 'links'))
                    {{ $audits->links() }}
                @endif
            </div>
        </div>
    </div>
</div>
@endsection