@extends('layouts.app')

@section('title', 'Entrepôts - KENAM SERVICES')

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
                <i class="fas fa-warehouse me-2 text-success"></i>Gestion des Entrepôts
            </h1>
            <p class="text-muted mb-0">Liste des entrepôts et capacités</p>
        </div>
        <a href="{{ route('stock.entrepots.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouvel Entrepôt
        </a>
    </div>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Liste des Entrepôts
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="entrepotsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Nom</th>
                            <th>Adresse</th>
                            <th>Responsable</th>
                            <th>Téléphone</th>
                            <th>Capacité</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entrepots as $entrepot)
                            <tr>
                                <td><code>{{ $entrepot->code }}</code></td>
                                <td class="fw-semibold">{{ $entrepot->nom }}</td>
                                <td>{{ $entrepot->adresse ?? '—' }}</td>
                                <td>{{ $entrepot->responsable ?? '—' }}</td>
                                <td>{{ $entrepot->telephone ?? '—' }}</td>
                                <td class="text-end">{{ $entrepot->capacite ? number_format($entrepot->capacite, 2, ',', ' ') : '—' }}</td>
                                <td>
                                    <span class="badge bg-{{ $entrepot->actif ? 'success' : 'secondary' }}">
                                        {{ $entrepot->actif ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    Aucun entrepôt enregistré
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($entrepots->hasPages())
                <div class="mt-3">
                    {{ $entrepots->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#entrepotsTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json' },
        order: [[1, 'asc']],
        pageLength: 25
    });
});
</script>
@endsection
