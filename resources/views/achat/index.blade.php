@extends('layouts.app')

@section('title', 'Module Achat')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800"><i class="fas fa-shopping-cart me-2"></i>Module Achat</h1>
            <p class="text-muted mb-0">Tous les achats liés aux activités, avec suivi comptable et trésorerie.</p>
        </div>
        <a href="{{ route('achat.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouvel achat
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card shadow-sm border-0"><div class="card-body"><div class="text-muted small text-uppercase">Achats</div><div class="h4 mb-0">{{ $stats['total'] ?? 0 }}</div></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm border-0"><div class="card-body"><div class="text-muted small text-uppercase">Montant total</div><div class="h4 mb-0">{{ number_format($stats['montant_total'] ?? 0, 0, ',', ' ') }} FCFA</div></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm border-0"><div class="card-body"><div class="text-muted small text-uppercase">Comptabilisés</div><div class="h4 mb-0">{{ $stats['valides'] ?? 0 }}</div></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm border-0"><div class="card-body"><div class="text-muted small text-uppercase">Décaissés</div><div class="h4 mb-0">{{ $stats['decaisses'] ?? 0 }}</div></div></div></div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('achat.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Recherche</label>
                    <input type="text" name="recherche" value="{{ request('recherche') }}" class="form-control" placeholder="Référence, fournisseur, objet...">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fournisseur</label>
                    <select name="fournisseur_id" class="form-select">
                        <option value="">Tous</option>
                        @foreach($fournisseurs as $fournisseur)
                            <option value="{{ $fournisseur->id }}" @selected((string) request('fournisseur_id') === (string) $fournisseur->id)>{{ $fournisseur->raison_sociale }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Type d'achat</label>
                    <input type="text" name="type_achat" value="{{ request('type_achat') }}" class="form-control" placeholder="Maintenance, fourniture, prestation...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="">Tous</option>
                        <option value="brouillon" @selected(request('statut') === 'brouillon')>Brouillon</option>
                        <option value="validee" @selected(request('statut') === 'validee')>Validé</option>
                        <option value="en_cours" @selected(request('statut') === 'en_cours')>En cours</option>
                        <option value="livree" @selected(request('statut') === 'livree')>Livré</option>
                    </select>
                </div>
                <div class="col-md-1 d-grid">
                    <button type="submit" class="btn btn-outline-primary"><i class="fas fa-filter"></i></button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Fournisseur</th>
                        <th>Type</th>
                        <th>Service</th>
                        <th>Montant</th>
                        <th>Compta</th>
                        <th>Trésorerie</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($achats as $achat)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $achat->reference }}</div>
                                <div class="small text-muted">{{ optional($achat->date_commande)->format('d/m/Y') }}</div>
                            </td>
                            <td>{{ $achat->fournisseur->raison_sociale ?? '-' }}</td>
                            <td>{{ $achat->type_achat ?? '-' }}</td>
                            <td>{{ $achat->service_concerne ?? '-' }}</td>
                            <td class="fw-semibold">{{ number_format($achat->montant_ttc ?? 0, 0, ',', ' ') }} FCFA</td>
                            <td>
                                @if($achat->expense)
                                    <span class="badge bg-success">Comptabilisé</span>
                                @else
                                    <span class="badge bg-secondary">En attente</span>
                                @endif
                            </td>
                            <td>
                                @if($achat->expense?->depenseCaisse)
                                    <span class="badge bg-success">Décaissé</span>
                                @elseif($achat->expense)
                                    <span class="badge bg-warning text-dark">À décaisser</span>
                                @else
                                    <span class="badge bg-secondary">N/A</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('achat.show', $achat) }}" class="btn btn-outline-primary"><i class="fas fa-eye"></i></a>
                                    @if(!$achat->expense)
                                        <form method="POST" action="{{ route('achat.valider', $achat) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success" title="Valider et comptabiliser"><i class="fas fa-check"></i></button>
                                        </form>
                                    @elseif(!$achat->expense->depenseCaisse)
                                        <a href="{{ route('achat.decaisser', $achat) }}" class="btn btn-outline-warning" title="Décaisser"><i class="fas fa-money-bill-wave"></i></a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">Aucun achat enregistré.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $achats->links() }}
        </div>
    </div>
</div>
@endsection