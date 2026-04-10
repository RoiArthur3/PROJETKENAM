@extends('layouts.app')

@section('title', 'Factures - Comptabilité - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-invoice-dollar me-2 text-primary"></i>Gestion des Factures
            </h1>
            <p class="text-muted mb-0">Module Comptabilité - Gestion des factures en FCFA</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('comptabilite.factures.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouvelle Facture
            </a>
            <form action="{{ route('comptabilite.importFactures') }}" method="POST" enctype="multipart/form-data" class="d-flex gap-2">
                @csrf
                <select name="client_id" class="form-select" required>
                    <option value="">Client...</option>
                    @foreach(($clientsForImport ?? []) as $client)
                        <option value="{{ $client->id }}">{{ $client->raison_sociale ?? $client->nom ?? $client->company_name ?? $client->contact_nom ?? ('Client #' . $client->id) }}</option>
                    @endforeach
                </select>
                <input type="file" name="factures_excel[]" class="form-control" accept=".xlsx,.xls,.csv" multiple required>
                <button type="submit" class="btn btn-warning text-nowrap">
                    <i class="fas fa-file-import me-2"></i>Importer Excel
                </button>
            </form>
            <a href="{{ route('comptabilite.factures.exportExcel', request()->query()) }}" class="btn btn-success">
                <i class="fas fa-file-excel me-2"></i>Télécharger Excel
            </a>
            <a href="{{ route('comptabilite.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour Tableau Bord
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-2 mb-3">
            <div class="card border-start border-primary border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Total</div>
                    <div class="h3 mb-0 text-primary">{{ $statsFactures['total'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="card border-start border-warning border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">En Attente</div>
                    <div class="h3 mb-0 text-warning">{{ $statsFactures['en_attente'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Payées</div>
                    <div class="h3 mb-0 text-success">{{ $statsFactures['payee'] }}</div>
                    <div class="small text-muted mt-1">{{ number_format($statsFactures['montant_paye'] ?? 0, 0, ',', ' ') }} FCFA</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-start border-danger border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Impayées</div>
                    <div class="h3 mb-0 text-danger">{{ $statsFactures['impayee'] }}</div>
                    <div class="small text-muted mt-1">{{ number_format($statsFactures['montant_impaye'] ?? 0, 0, ',', ' ') }} FCFA</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-start border-dark border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">En Retard</div>
                    <div class="h3 mb-0 text-dark">{{ $statsFactures['en_retard'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="GET" action="{{ route('comptabilite.factures.index') }}" class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Rechercher par numéro de facture ou nom du client..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-outline-primary flex-fill">
                            <i class="fas fa-filter me-1"></i>Filtrer
                        </button>
                        <a href="{{ route('comptabilite.factures.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Réinitialiser
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des factures -->
    <div class="card shadow-sm">
        <div class="card-header bg-light py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Liste des Factures
                <span class="badge bg-secondary ms-2">{{ $factures->total() }}</span>
            </h6>
        </div>
        <div class="card-body">
            @if($factures->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Numéro</th>
                                <th>Client</th>
                                <th>Date</th>
                                <th>Échéance</th>
                                <th>Montant HT</th>
                                <th>TVA</th>
                                <th>Montant TTC</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($factures as $facture)
                                <tr>
                                    <td>
                                        <strong>{{ $facture->numero }}</strong>
                                    </td>
                                    <td>{{ $facture->client_nom }}</td>
                                    <td>{{ \Carbon\Carbon::parse($facture->date_facture)->format('d/m/Y') }}</td>
                                    <td>
                                        @if(isset($facture->date_echeance) && $facture->date_echeance)
                                            {{ \Carbon\Carbon::parse($facture->date_echeance)->format('d/m/Y') }}
                                            @if($facture->statut == 'impayee' && \Carbon\Carbon::parse($facture->date_echeance)->isPast())
                                                <span class="badge bg-danger ms-1">En retard</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">{{ number_format($facture->montant_ht, 0, ',', ' ') }} FCFA</td>
                                    <td class="text-end">{{ $facture->tva }}%</td>
                                    <td class="text-end fw-bold">{{ number_format($facture->montant_ttc, 0, ',', ' ') }} FCFA</td>
                                    <td>
                                        @switch($facture->statut)
                                            @case('payee')
                                                <span class="badge bg-success">Payée</span>
                                                @break
                                            @case('envoyee')
                                                <span class="badge bg-primary">Envoyée</span>
                                                @break
                                            @case('en_attente')
                                                <span class="badge bg-warning">En attente</span>
                                                @break
                                            @case('impayee')
                                                <span class="badge bg-danger">Impayée</span>
                                                @break
                                            @case('annulee')
                                                <span class="badge bg-secondary">Annulée</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ $facture->statut }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('comptabilite.factures.show', $facture->id) }}" class="btn btn-sm btn-outline-primary" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('comptabilite.factures.edit', $facture->id) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($facture->statut == 'impayee')
                                                <button type="button" class="btn btn-sm btn-outline-success" title="Marquer comme payée">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Supprimer" onclick="confirmDelete({{ $facture->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Affichage de {{ $factures->firstItem() }} à {{ $factures->lastItem() }} sur {{ $factures->total() }} factures
                    </div>
                    {{ $factures->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucune facture trouvée</h5>
                    <p class="text-muted">
                        @if(request('search'))
                            Aucune facture ne correspond à votre recherche.
                        @else
                            Vous n'avez pas encore créé de facture.
                        @endif
                    </p>
                    <a href="{{ route('comptabilite.factures.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Créer la première facture
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Formulaire de suppression -->
<form id="deleteForm" method="POST" action="" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette facture ?')) {
        const form = document.getElementById('deleteForm');
        form.action = '{{ route("comptabilite.factures.destroy", ":id") }}'.replace(':id', id);
        form.submit();
    }
}
</script>
@endsection
