@extends('layouts.app')

@section('title', 'Entrées de Stock - KENAM SERVICES')

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
                <i class="fas fa-arrow-down me-2 text-success"></i>Entrées de Stock
            </h1>
            <p class="text-muted mb-0">Gestion des réceptions et approvisionnements</p>
        </div>
        <a href="{{ route('stock.entrees.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouvelle Entrée
        </a>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Total Entrées</div>
                    <div class="h3 mb-0 text-success">{{ $entrees->total() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-primary border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Ce Mois</div>
                    <div class="h3 mb-0 text-primary">{{ $entrees->where('date', '>=', now()->startOfMonth())->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-info border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Valeur Totale</div>
                    <div class="h3 mb-0 text-info">{{ number_format($entrees->sum('montant_total'), 0, ',', ' ') }} FCFA</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-warning border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Dernière Entrée</div>
                    <div class="h5 mb-0 text-warning">{{ $entrees->first()?->date?->format('d/m/Y') ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Liste des Entrées
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="entreesTable">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Produit</th>
                            <th>Quantité</th>
                            <th>Prix Unit.</th>
                            <th>Montant Total</th>
                            <th>Fournisseur</th>
                            <th>Référence</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entrees as $entree)
                            <tr>
                                <td>{{ $entree->date->format('d/m/Y') }}</td>
                                <td>
                                    @php
                                        $badgeClass = [
                                            'Achat' => 'success',
                                            'Retour' => 'info',
                                            'Ajustement' => 'warning',
                                            'Transfert' => 'primary',
                                        ][$entree->type] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $badgeClass }}">{{ $entree->type }}</span>
                                </td>
                                <td class="fw-semibold">{{ $entree->produit_nom }}</td>
                                <td class="text-end">{{ number_format($entree->quantite, 2, ',', ' ') }}</td>
                                <td class="text-end">{{ $entree->prix_unitaire ? number_format($entree->prix_unitaire, 0, ',', ' ') . ' FCFA' : '—' }}</td>
                                <td class="text-end fw-bold text-success">{{ $entree->montant_total ? number_format($entree->montant_total, 0, ',', ' ') . ' FCFA' : '—' }}</td>
                                <td>{{ $entree->fournisseur ?? '—' }}</td>
                                <td><small>{{ $entree->reference ?? '—' }}</small></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-info" title="Voir détails" data-bs-toggle="modal" data-bs-target="#detailModal{{ $entree->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal Détails -->
                            <div class="modal fade" id="detailModal{{ $entree->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title">Détails Entrée #{{ $entree->id }}</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <dl class="row">
                                                <dt class="col-sm-4">Date:</dt>
                                                <dd class="col-sm-8">{{ $entree->date->format('d/m/Y') }}</dd>
                                                
                                                <dt class="col-sm-4">Type:</dt>
                                                <dd class="col-sm-8"><span class="badge bg-{{ $badgeClass }}">{{ $entree->type }}</span></dd>
                                                
                                                <dt class="col-sm-4">Produit:</dt>
                                                <dd class="col-sm-8">{{ $entree->produit_nom }}</dd>
                                                
                                                <dt class="col-sm-4">Quantité:</dt>
                                                <dd class="col-sm-8">{{ number_format($entree->quantite, 2, ',', ' ') }}</dd>
                                                
                                                <dt class="col-sm-4">Prix unitaire:</dt>
                                                <dd class="col-sm-8">{{ $entree->prix_unitaire ? number_format($entree->prix_unitaire, 0, ',', ' ') . ' FCFA' : 'Non renseigné' }}</dd>
                                                
                                                <dt class="col-sm-4">Montant total:</dt>
                                                <dd class="col-sm-8 fw-bold text-success">{{ $entree->montant_total ? number_format($entree->montant_total, 0, ',', ' ') . ' FCFA' : 'Non calculé' }}</dd>
                                                
                                                <dt class="col-sm-4">Fournisseur:</dt>
                                                <dd class="col-sm-8">{{ $entree->fournisseur ?? 'Non renseigné' }}</dd>
                                                
                                                <dt class="col-sm-4">Référence:</dt>
                                                <dd class="col-sm-8">{{ $entree->reference ?? 'Non renseignée' }}</dd>
                                                
                                                @if($entree->notes)
                                                    <dt class="col-sm-4">Notes:</dt>
                                                    <dd class="col-sm-8">{{ $entree->notes }}</dd>
                                                @endif
                                                
                                                @if($entree->user)
                                                    <dt class="col-sm-4">Créé par:</dt>
                                                    <dd class="col-sm-8">{{ $entree->user->name }}</dd>
                                                @endif
                                                
                                                <dt class="col-sm-4">Créé le:</dt>
                                                <dd class="col-sm-8">{{ $entree->created_at->format('d/m/Y H:i') }}</dd>
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
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    Aucune entrée enregistrée
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($entrees->hasPages())
                <div class="mt-3">
                    {{ $entrees->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#entreesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
        },
        order: [[0, 'desc']],
        pageLength: 25
    });
});
</script>
@endsection
