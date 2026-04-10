@extends('layouts.app')

@section('title', 'Opérations - À valider')

@push('styles')
<style>
    .priority-badge {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
    }

    .priority-urgente { background-color: #f8d7da; color: #842029; }
    .priority-haute { background-color: #fff3cd; color: #664d03; }
    .priority-moyenne { background-color: #cfe2ff; color: #084298; }
    .priority-basse { background-color: #d1e7dd; color: #0f5132; }

    .status-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
    }

    .status-pending { background-color: #fff3cd; color: #856404; }
    .status-approved { background-color: #d4edda; color: #155724; }
    .status-rejected { background-color: #f8d7da; color: #721c24; }

    .action-btn {
        width: 30px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin: 0 2px;
    }
</style>
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
@endpush

@section('content')
<div class="container-fluid">


    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-tasks me-2"></i>Liste des opérations
            </h6>
            <div class="d-flex">
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="operationsTable">
                <thead class="table-light">
                    <tr>
                        <th>Référence</th>
                        <th>Titre</th>
                        <th>Demandeur</th>
                        <th>Priorité</th>
                        <th>Échéance</th>
                        <th>Statut</th>
                        <th>Date de création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($operations as $operation)
                        <tr>
                            <td>{{ str_pad($operation->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <a href="{{ route('operations.show', $operation) }}" class="text-primary">
                                    {{ Str::limit($operation->titre, 30) }}
                                </a>
                            </td>
                            <td>{{ $operation->demandeur_name ?? 'N/A' }}</td>
                            <td>
                                @php
                                    $priorityClass = [
                                        'urgente' => 'priority-urgente',
                                        'haute' => 'priority-haute',
                                        'moyenne' => 'priority-moyenne',
                                        'basse' => 'priority-basse'
                                    ][$operation->priorite ?? 'moyenne'] ?? 'priority-moyenne';
                                @endphp
                                <span class="priority-badge {{ $priorityClass }}">
                                    {{ ucfirst($operation->priorite ?? 'moyenne') }}
                                </span>
                            </td>
                            <td>
                                @if($operation->echeance)
                                    {{ \Carbon\Carbon::parse($operation->echeance)->format('d/m/Y') }}
                                    @if(\Carbon\Carbon::parse($operation->echeance)->isPast())
                                        <span class="badge bg-danger ms-1">En retard</span>
                                    @endif
                                @else
                                    <span class="text-muted">Non définie</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusClass = [
                                        'pending_validation' => 'status-pending',
                                        'en_cours' => 'status-pending',
                                        'termine' => 'status-approved',
                                        'rejete' => 'status-rejected'
                                    ][$operation->statut_courant ?? 'pending_validation'] ?? 'status-pending';
                                @endphp
                                <span class="status-badge {{ $statusClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $operation->statut_courant ?? 'pending_validation')) }}
                                </span>
                            </td>
                            <td>{{ $operation->created_at ? $operation->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                            <td class="text-nowrap">
                                <a href="{{ route('operations.show', $operation) }}"
                                   class="btn btn-sm btn-outline-primary action-btn"
                                   title="Voir les détails">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @if(($operation->statut_courant ?? 'pending_validation') === 'pending_validation')
                                    <form action="{{ route('operations.validate', $operation->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" name="status" value="approved"
                                                class="btn btn-sm btn-outline-success action-btn"
                                                title="Approuver"
                                                onclick="return confirm('Êtes-vous sûr de vouloir approuver cette opération ?')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>

                                    <button type="button" class="btn btn-sm btn-outline-danger action-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#rejectModal{{ $operation->id }}"
                                            title="Rejeter">
                                        <i class="fas fa-times"></i>
                                    </button>

                                    <!-- Modal de rejet -->
                                    <div class="modal fade" id="rejectModal{{ $operation->id }}" tabindex="-1"
                                         aria-labelledby="rejectModalLabel{{ $operation->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title" id="rejectModalLabel{{ $operation->id }}">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>Rejeter l'opération
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('operations.validate', $operation->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="rejected">
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="reject_reason{{ $operation->id }}" class="form-label">Raison du rejet</label>
                                                            <textarea class="form-control" id="reject_reason{{ $operation->id }}" name="reject_reason" rows="3" required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <button type="submit" class="btn btn-danger">Rejeter l'opération</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p class="mb-0">Aucune opération en attente</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($operations->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $operations->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Attendre que tous les éléments soient chargés
        setTimeout(function() {
            try {
                if ($('#operationsTable').length && $('#operationsTable tbody tr').length > 0) {
                    $('#operationsTable').DataTable({
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
                        },
                        order: [[0, 'desc']],
                        pageLength: 15,
                        responsive: true,
                        columnDefs: [
                            { responsivePriority: 1, targets: 0 },
                            { responsivePriority: 2, targets: 1 },
                            { responsivePriority: 3, targets: -1 }
                        ]
                    });
                }
            } catch (error) {
                console.error('Erreur lors de l\'initialisation de DataTables:', error);
            }
        }, 100);
    });
</script>
@endpush

@endsection
