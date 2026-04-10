@extends('layouts.app')

@section('title', 'Inventaires - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-clipboard-check me-2 text-secondary"></i>Inventaires
            </h1>
            <p class="text-muted mb-0">Suivi des inventaires d'entrepôt</p>
        </div>
        <a href="{{ route('stock.inventaire.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouvel Inventaire
        </a>
    </div>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Liste des Inventaires
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="inventairesTable">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Entrepôt</th>
                            <th>Responsable</th>
                            <th>Type</th>
                            <th>Statut</th>
                            <th>Créé le</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventaires as $inventaire)
                            <tr>
                                <td>{{ $inventaire->date?->format('d/m/Y') }}</td>
                                <td class="fw-semibold">{{ $inventaire->entrepot_nom }}</td>
                                <td>{{ $inventaire->responsable }}</td>
                                <td><span class="badge bg-secondary">{{ $inventaire->type }}</span></td>
                                <td>
                                    @php
                                        $badge = ['En cours' => 'warning', 'Terminé' => 'success', 'Annulé' => 'secondary'][$inventaire->statut] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $badge }}">{{ $inventaire->statut }}</span>
                                </td>
                                <td>{{ $inventaire->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    Aucun inventaire enregistré
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($inventaires->hasPages())
                <div class="mt-3">
                    {{ $inventaires->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#inventairesTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json' },
        order: [[0, 'desc']],
        pageLength: 25
    });
});
</script>
@endsection
