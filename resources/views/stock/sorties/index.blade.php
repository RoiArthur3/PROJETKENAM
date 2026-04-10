@extends('layouts.app')

@section('title', 'Sorties de Stock - KENAM SERVICES')

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
                <i class="fas fa-arrow-up me-2 text-danger"></i>Sorties de Stock
            </h1>
            <p class="text-muted mb-0">Gestion des sorties et consommations</p>
        </div>
        <a href="{{ route('stock.exits.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouvelle Sortie
        </a>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-start border-danger border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Total Sorties</div>
                    <div class="h3 mb-0 text-danger">{{ $sorties->total() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-primary border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Ce Mois</div>
                    <div class="h3 mb-0 text-primary">{{ $sorties->where('date', '>=', now()->startOfMonth())->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-warning border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Utilisations</div>
                    <div class="h3 mb-0 text-warning">{{ $sorties->where('type', 'Utilisation')->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-info border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Dernière Sortie</div>
                    <div class="h5 mb-0 text-info">{{ $sorties->first()?->date?->format('d/m/Y') ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Liste des Sorties
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="sortiesTable">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Matériel</th>
                            <th>Quantité</th>
                            <th>Destinataire</th>
                            <th>Demandeur</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sorties as $sortie)
                            <tr>
                                <td>{{ $sortie->date->format('d/m/Y') }}</td>
                                <td>
                                    @php
                                        $badgeClass = [
                                            'Utilisation' => 'primary',
                                            'Perte' => 'danger',
                                            'Casse' => 'warning',
                                            'Retour' => 'info',
                                            'Autre' => 'secondary',
                                        ][$sortie->type] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $badgeClass }}">{{ $sortie->type }}</span>
                                </td>
                                <td class="fw-semibold">{{ $sortie->produit_nom }}</td>
                                <td class="text-end">{{ number_format($sortie->quantite, 2, ',', ' ') }}</td>
                                <td>{{ $sortie->destinataire ?? '—' }}</td>
                                <td>{{ $sortie->demandeur ?? '—' }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-info" title="Voir détails" data-bs-toggle="modal" data-bs-target="#detailModal{{ $sortie->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal Détails -->
                            <div class="modal fade" id="detailModal{{ $sortie->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title">Détails Sortie #{{ $sortie->id }}</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <dl class="row">
                                                <dt class="col-sm-5">Date:</dt>
                                                <dd class="col-sm-7">{{ $sortie->date->format('d/m/Y') }}</dd>
                                                
                                                <dt class="col-sm-5">Type:</dt>
                                                <dd class="col-sm-7"><span class="badge bg-{{ $badgeClass }}">{{ $sortie->type }}</span></dd>
                                                
                                                <dt class="col-sm-5">Matériel:</dt>
                                                <dd class="col-sm-7">{{ $sortie->produit_nom }}</dd>
                                                
                                                <dt class="col-sm-5">Quantité:</dt>
                                                <dd class="col-sm-7">{{ number_format($sortie->quantite, 2, ',', ' ') }}</dd>
                                                
                                                <dt class="col-sm-5">Destinataire:</dt>
                                                <dd class="col-sm-7">{{ $sortie->destinataire ?? 'Non renseigné' }}</dd>
                                                
                                                <dt class="col-sm-5">Demandeur:</dt>
                                                <dd class="col-sm-7">{{ $sortie->demandeur ?? 'Non renseigné' }}</dd>
                                                
                                                @if($sortie->motif)
                                                    <dt class="col-sm-5">Motif:</dt>
                                                    <dd class="col-sm-7">{{ $sortie->motif }}</dd>
                                                @endif
                                                
                                                @if($sortie->user)
                                                    <dt class="col-sm-5">Créé par:</dt>
                                                    <dd class="col-sm-7">{{ $sortie->user->name }}</dd>
                                                @endif
                                                
                                                <dt class="col-sm-5">Créé le:</dt>
                                                <dd class="col-sm-7">{{ $sortie->created_at->format('d/m/Y H:i') }}</dd>
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
                                    Aucune sortie enregistrée
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($sorties->hasPages())
                <div class="mt-3">
                    {{ $sorties->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#sortiesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
        },
        order: [[0, 'desc']],
        pageLength: 25
    });
});
</script>
@endsection
