@extends('layouts.app')

@section('title', 'Facture fournisseur ' . ($facture->reference ?? $facture->numero_facture))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-file-invoice me-2 text-primary"></i>
            Facture {{ $facture->numero_facture }} ({{ $facture->reference }})
        </h1>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Retour
        </a>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white"><strong>Détails de la facture</strong></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Fournisseur</label>
                            <div>{{ $fournisseur->nom ?? $fournisseur->raison_sociale ?? $fournisseur->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small">Date facture</label>
                            <div>{{ optional($facture->date_facture)->format('Y-m-d') }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small">Échéance</label>
                            <div>{{ optional($facture->date_echeance)->format('Y-m-d') }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Montant TTC</label>
                            <div class="fw-bold text-primary">{{ number_format((float)$facture->montant_ttc, 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Payé</label>
                            <div class="text-success">{{ number_format((float)$montantPaye, 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Reste à payer</label>
                            <div class="text-danger fw-bold">{{ number_format((float)$resteAPayer, 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small">Notes</label>
                            <div>{{ $facture->notes ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            @if($facture->depensesCaisses->count() > 0)
                <div class="card shadow-sm">
                    <div class="card-header bg-white"><strong>Décaissements de caisse liés</strong></div>
                    <div class="card-body table-responsive">
                        <table class="table table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Caisse</th>
                                    <th>Date</th>
                                    <th>Montant</th>
                                    <th>Mode</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($facture->depensesCaisses as $depense)
                                    <tr>
                                        <td>{{ $depense->reference ?? $depense->id }}</td>
                                        <td>{{ optional($depense->caisse)->nom ?? '-' }}</td>
                                        <td>{{ optional($depense->date_depense)->format('Y-m-d') }}</td>
                                        <td>{{ number_format((float)$depense->montant, 0, ',', ' ') }} FCFA</td>
                                        <td>{{ $depense->mode_paiement }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white"><strong>Paiements enregistrés</strong></div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @forelse($facture->paiements as $paiement)
                            <li class="mb-2">
                                <i class="fas fa-receipt me-2"></i>
                                {{ optional($paiement->date_paiement)->format('Y-m-d') }} —
                                {{ number_format((float)$paiement->montant, 0, ',', ' ') }} FCFA
                                <span class="text-muted">({{ $paiement->mode_paiement }})</span>
                            </li>
                        @empty
                            <li class="text-muted">Aucun paiement enregistré.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            @if($resteAPayer > 0)
                <div class="card shadow-sm">
                    <div class="card-header bg-white"><strong>Payer via caisse</strong></div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('fournisseurs.factures.payer-caisse', [$fournisseur->id, $facture->id]) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Caisse à utiliser <span class="text-danger">*</span></label>
                                <select name="caisse_id" class="form-select @error('caisse_id') is-invalid @enderror" required>
                                    <option value="">Sélectionner une caisse</option>
                                    @foreach($caisses as $caisse)
                                        <option value="{{ $caisse->id }}" {{ old('caisse_id') == $caisse->id ? 'selected' : '' }}>
                                            {{ $caisse->nom }} ({{ number_format((float)$caisse->solde_actuel, 0, ',', ' ') }} FCFA)
                                        </option>
                                    @endforeach
                                </select>
                                @error('caisse_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Montant à payer <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" min="0.01" step="0.01" name="montant" value="{{ old('montant', $resteAPayer) }}" class="form-control @error('montant') is-invalid @enderror" required>
                                    <span class="input-group-text">FCFA</span>
                                    @error('montant')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Date de paiement <span class="text-danger">*</span></label>
                                <input type="date" name="date_paiement" value="{{ old('date_paiement', now()->format('Y-m-d')) }}" class="form-control @error('date_paiement') is-invalid @enderror" required>
                                @error('date_paiement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Mode de paiement</label>
                                <select name="mode_paiement" class="form-select @error('mode_paiement') is-invalid @enderror">
                                    <option value="especes" {{ old('mode_paiement') === 'especes' ? 'selected' : '' }}>Espèces (caisse)</option>
                                    <option value="cheque" {{ old('mode_paiement') === 'cheque' ? 'selected' : '' }}>Chèque</option>
                                    <option value="virement" {{ old('mode_paiement') === 'virement' ? 'selected' : '' }}>Virement</option>
                                    <option value="autre" {{ old('mode_paiement') === 'autre' ? 'selected' : '' }}>Autre</option>
                                </select>
                                @error('mode_paiement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notes (optionnel)</label>
                                <textarea name="notes" rows="2" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-cash-register me-1"></i> Enregistrer le paiement via caisse
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
