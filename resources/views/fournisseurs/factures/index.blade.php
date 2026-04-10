@extends('layouts.app')

@section('title', 'Factures fournisseur')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Factures fournisseurs</h1>
            <p class="text-muted mb-0">
                @if(isset($fournisseur) && $fournisseur)
                    Fournisseur: {{ $fournisseur->raison_sociale ?? $fournisseur->nom ?? $fournisseur->name }}
                @else
                    Liste globale de toutes les factures fournisseurs
                @endif
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('fournisseurs.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Dashboard
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-light h-100">
                <div class="card-body py-3">
                    <div class="small text-muted">Total factures</div>
                    <div class="h5 mb-0 fw-bold">{{ number_format((float)($stats['total'] ?? 0), 0, ',', ' ') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-light h-100">
                <div class="card-body py-3">
                    <div class="small text-muted">Montant total</div>
                    <div class="h5 mb-0 fw-bold">{{ number_format((float)($stats['montant_total'] ?? 0), 0, ',', ' ') }} FCFA</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-light h-100">
                <div class="card-body py-3">
                    <div class="small text-muted">Paye</div>
                    <div class="h5 mb-0 fw-bold text-success">{{ number_format((float)($stats['montant_paye'] ?? 0), 0, ',', ' ') }} FCFA</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-light h-100">
                <div class="card-body py-3">
                    <div class="small text-muted">Reste a payer</div>
                    <div class="h5 mb-0 fw-bold text-danger">{{ number_format((float)($stats['montant_restant'] ?? 0), 0, ',', ' ') }} FCFA</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-sm table-hover align-middle">
                <thead>
                    <tr>
                        <th>Fournisseur</th>
                        <th>Référence</th>
                        <th>N° facture</th>
                        <th>Date</th>
                        <th>Échéance</th>
                        <th>Montant TTC</th>
                        <th>Payé</th>
                        <th>Reste</th>
                        <th>Statut</th>
                        <th>Pièce(s) jointe(s)</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($factures as $facture)
                        <tr>
                            <td>{{ $facture->fournisseur->raison_sociale ?? $facture->fournisseur->nom ?? '-' }}</td>
                            <td>{{ $facture->reference }}</td>
                            <td>{{ $facture->numero_facture }}</td>
                            <td>{{ optional($facture->date_facture)->format('Y-m-d') }}</td>
                            <td>{{ optional($facture->date_echeance)->format('Y-m-d') }}</td>
                            <td>{{ number_format((float)$facture->montant_ttc, 0, ',', ' ') }} FCFA</td>
                            <td>{{ number_format((float)$facture->montant_paye, 0, ',', ' ') }} FCFA</td>
                            <td>{{ number_format((float)$facture->reste_a_payer, 0, ',', ' ') }} FCFA</td>
                            <td>
                                @php
                                    $statut = $facture->statut;
                                    $class = $statut === 'payee' ? 'success' : ($statut === 'partiellement_payee' ? 'warning text-dark' : 'danger');
                                @endphp
                                <span class="badge bg-{{ $class }}">{{ ucfirst(str_replace('_', ' ', $statut)) }}</span>
                            </td>
                            <td>
                                @php
                                    $pieces = $documentsParFacture[$facture->id] ?? collect();
                                @endphp
                                @if($pieces->count() > 0)
                                    @foreach($pieces as $piece)
                                        <a href="{{ asset('storage/' . ltrim($piece->chemin_fichier, '/')) }}" target="_blank" class="badge bg-info-subtle text-info text-decoration-none me-1">
                                            <i class="fas fa-paperclip me-1"></i>{{ $piece->nom_fichier }}
                                        </a>
                                    @endforeach
                                @else
                                    <span class="text-muted">Aucune</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('fournisseurs.factures.show', ['fournisseur' => $facture->fournisseur_id, 'facture' => $facture->id]) }}" class="btn btn-sm btn-outline-primary">Détails</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted">Aucune facture trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($factures, 'links'))
            <div class="card-footer bg-white">
                {{ $factures->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
