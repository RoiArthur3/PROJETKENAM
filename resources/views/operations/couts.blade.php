@extends('layouts.app')

@section('title', "Coûts & Achats de l'Opération")

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Coûts & Achats • Opération #{{ $operation->id }} — {{ $operation->titre }}</h1>
        <a href="{{ route('operations.show', $operation->id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Détails
        </a>
    </div>

    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Historique des coûts</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>Fournisseur</th>
                                    <th class="text-end">Montant</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($costs as $c)
                                <tr>
                                    <td><span class="badge {{ $c->type === 'facture' ? 'bg-info' : 'bg-warning' }}">{{ ucfirst($c->type) }}</span></td>
                                    <td>{{ $c->description }}</td>
                                    <td>{{ $c->fournisseur ?? '—' }}</td>
                                    <td class="text-end">{{ number_format($c->montant, 0, ',', ' ') }} FCFA</td>
                                    <td>{{ $c->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-muted">Aucun coût enregistré.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ajouter un coût</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('operations.costs.store', $operation->id) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <select class="form-select" name="type" required>
                                <option value="depense">Dépense</option>
                                <option value="facture">Facture</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <input type="text" name="description" class="form-control" placeholder="Ex: Achat batterie véhicule" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Montant (FCFA)</label>
                                <input type="number" name="montant" min="0" step="0.01" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fournisseur (optionnel)</label>
                                <input type="text" name="fournisseur" class="form-control" placeholder="Ex: Total Energies">
                            </div>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
