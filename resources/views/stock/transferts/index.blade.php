@extends('layouts.app')

@section('title', 'Transferts de Stock - KENAM SERVICES')

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
                <i class="fas fa-exchange-alt me-2 text-primary"></i>Transferts de Stock
            </h1>
            <p class="text-muted mb-0">Gestion des mouvements entre entrepôts</p>
        </div>
        <a href="{{ route('warehouse.transferts.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouveau Transfert
        </a>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-start border-primary border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Total Transferts</div>
                    <div class="h3 mb-0 text-primary">{{ $transferts->total() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-warning border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">En Attente</div>
                    <div class="h3 mb-0 text-warning">{{ $transferts->where('statut', 'En attente')->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-info border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">En Cours</div>
                    <div class="h3 mb-0 text-info">{{ $transferts->where('statut', 'En cours')->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Terminés</div>
                    <div class="h3 mb-0 text-success">{{ $transferts->where('statut', 'Terminé')->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Liste des Transferts
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="transfertsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Matériel</th>
                            <th>Quantité</th>
                            <th>Source</th>
                            <th>Destination</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transferts as $transfert)
                            <tr>
                                <td>{{ $transfert->date->format('d/m/Y') }}</td>
                                <td class="fw-semibold">{{ $transfert->produit_nom }}</td>
                                <td class="text-end">{{ number_format($transfert->quantite, 2, ',', ' ') }}</td>
                                <td><span class="badge bg-secondary">{{ $transfert->entrepot_source }}</span></td>
                                <td><span class="badge bg-info">{{ $transfert->entrepot_destination }}</span></td>
                                <td>
                                    @php
                                        $statutBadge = [
                                            'En attente' => 'warning',
                                            'En cours' => 'info',
                                            'Terminé' => 'success',
                                            'Annulé' => 'danger',
                                        ][$transfert->statut] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $statutBadge }}">{{ $transfert->statut }}</span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-info" title="Voir détails" data-bs-toggle="modal" data-bs-target="#detailModal{{ $transfert->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal Détails -->
                            <div class="modal fade" id="detailModal{{ $transfert->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title">Détails Transfert #{{ $transfert->id }}</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <dl class="row">
                                                <dt class="col-sm-5">Date:</dt>
                                                <dd class="col-sm-7">{{ $transfert->date->format('d/m/Y') }}</dd>

                                                <dt class="col-sm-5">Matériel:</dt>
                                                <dd class="col-sm-7">{{ $transfert->produit_nom }}</dd>

                                                <dt class="col-sm-5">Quantité:</dt>
                                                <dd class="col-sm-7">{{ number_format($transfert->quantite, 2, ',', ' ') }}</dd>

                                                <dt class="col-sm-5">Entrepôt source:</dt>
                                                <dd class="col-sm-7"><span class="badge bg-secondary">{{ $transfert->entrepot_source }}</span></dd>

                                                <dt class="col-sm-5">Entrepôt destination:</dt>
                                                <dd class="col-sm-7"><span class="badge bg-info">{{ $transfert->entrepot_destination }}</span></dd>

                                                <dt class="col-sm-5">Statut:</dt>
                                                <dd class="col-sm-7"><span class="badge bg-{{ $statutBadge }}">{{ $transfert->statut }}</span></dd>

                                                @if($transfert->motif)
                                                    <dt class="col-sm-5">Motif:</dt>
                                                    <dd class="col-sm-7">{{ $transfert->motif }}</dd>
                                                @endif

                                                @if($transfert->user)
                                                    <dt class="col-sm-5">Créé par:</dt>
                                                    <dd class="col-sm-7">{{ $transfert->user->name }}</dd>
                                                @endif

                                                <dt class="col-sm-5">Créé le:</dt>
                                                <dd class="col-sm-7">{{ $transfert->created_at->format('d/m/Y H:i') }}</dd>
                                            </dl>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    Aucun transfert enregistré
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transferts->hasPages())
                <div class="mt-3">
                    {{ $transferts->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#transfertsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
        },
        order: [[0, 'desc']],
        pageLength: 25
    });
});
</script>
@endsection
