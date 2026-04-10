@extends('layouts.app')

@section('title', 'Détail Achat')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Achat {{ $achat->reference }}</h1>
            <div class="text-muted">{{ $achat->fournisseur->raison_sociale ?? '-' }} | {{ $achat->type_achat ?? 'Achat' }}</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('achat.index') }}" class="btn btn-outline-secondary">Retour</a>
            @if(!$achat->expense)
                <form method="POST" action="{{ route('achat.valider', $achat) }}">
                    @csrf
                    <button type="submit" class="btn btn-success"><i class="fas fa-check me-2"></i>Valider et comptabiliser</button>
                </form>
            @elseif(!$achat->expense->depenseCaisse)
                <a href="{{ route('achat.decaisser', $achat) }}" class="btn btn-warning"><i class="fas fa-money-bill-wave me-2"></i>Décaisser</a>
            @else
                <a href="{{ route('tresorerie.depenses.show', $achat->expense->depenseCaisse) }}" class="btn btn-outline-primary"><i class="fas fa-eye me-2"></i>Voir le décaissement</a>
            @endif
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card shadow-sm border-0"><div class="card-body"><div class="small text-muted text-uppercase">Montant TTC</div><div class="h4 mb-0">{{ number_format($achat->montant_ttc ?? 0, 0, ',', ' ') }} FCFA</div></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm border-0"><div class="card-body"><div class="small text-muted text-uppercase">Statut achat</div><div class="h5 mb-0">{{ ucfirst(str_replace('_', ' ', $achat->statut ?? 'brouillon')) }}</div></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm border-0"><div class="card-body"><div class="small text-muted text-uppercase">Comptabilité</div><div class="h5 mb-0">{{ $achat->expense ? 'Synchronisé' : 'En attente' }}</div></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm border-0"><div class="card-body"><div class="small text-muted text-uppercase">Trésorerie</div><div class="h5 mb-0">{{ $achat->expense?->depenseCaisse ? 'Décaissé' : 'Non payé' }}</div></div></div></div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white"><strong>Lignes d'achat</strong></div>
                <div class="card-body table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Désignation</th>
                                <th>Quantité</th>
                                <th>PU HT</th>
                                <th>TVA</th>
                                <th>Total TTC</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($achat->lignes as $ligne)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $ligne->designation }}</div>
                                        @if($ligne->description)<div class="text-muted small">{{ $ligne->description }}</div>@endif
                                    </td>
                                    <td>{{ number_format($ligne->quantite, 2, ',', ' ') }} {{ $ligne->unite }}</td>
                                    <td>{{ number_format($ligne->prix_unitaire_ht, 0, ',', ' ') }}</td>
                                    <td>{{ number_format($ligne->tva_taux, 0, ',', ' ') }}%</td>
                                    <td class="fw-semibold">{{ number_format($ligne->montant_ttc, 0, ',', ' ') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong>Factures fournisseurs</strong>
                    <span class="badge bg-light text-dark">{{ $achat->factures->count() }}</span>
                </div>
                <div class="card-body">
                    @if($achat->factures->isEmpty())
                        <form method="POST" action="{{ route('achat.factures.store', $achat) }}" class="row g-3">
                            @csrf
                            <div class="col-md-4">
                                <label class="form-label">Numéro de facture</label>
                                <input type="text" name="numero_facture" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Référence</label>
                                <input type="text" name="reference" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Conditions de paiement</label>
                                <input type="text" name="conditions_paiement" class="form-control" value="30 jours" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Date facture</label>
                                <input type="date" name="date_facture" value="{{ now()->format('Y-m-d') }}" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Date échéance</label>
                                <input type="date" name="date_echeance" value="{{ now()->addDays(30)->format('Y-m-d') }}" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Notes</label>
                                <input type="text" name="notes" class="form-control">
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-file-invoice me-2"></i>Créer la facture fournisseur</button>
                            </div>
                        </form>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Facture</th>
                                        <th>Date</th>
                                        <th>Échéance</th>
                                        <th>Montant TTC</th>
                                        <th>Payé</th>
                                        <th>Reste</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($achat->factures as $facture)
                                        @php
                                            $montantPaye = (float) $facture->paiements->sum('montant');
                                            $resteAPayer = max(0, (float) $facture->montant_ttc - $montantPaye);
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $facture->numero_facture }}</div>
                                                <div class="small text-muted">{{ $facture->reference }}</div>
                                            </td>
                                            <td>{{ optional($facture->date_facture)->format('d/m/Y') }}</td>
                                            <td>{{ optional($facture->date_echeance)->format('d/m/Y') }}</td>
                                            <td>{{ number_format((float) $facture->montant_ttc, 0, ',', ' ') }} FCFA</td>
                                            <td>{{ number_format($montantPaye, 0, ',', ' ') }} FCFA</td>
                                            <td class="fw-semibold">{{ number_format($resteAPayer, 0, ',', ' ') }} FCFA</td>
                                            <td><span class="badge bg-{{ $resteAPayer <= 0 ? 'success' : ($montantPaye > 0 ? 'warning text-dark' : 'secondary') }}">{{ $facture->statut }}</span></td>
                                            <td>
                                                <a href="{{ route('fournisseurs.factures.show', [$achat->fournisseur_id, $facture->id]) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @if($resteAPayer > 0)
                                            <tr>
                                                <td colspan="8" class="bg-light">
                                                    <form method="POST" action="{{ route('achat.paiements.store', [$achat, $facture]) }}" class="row g-2 align-items-end">
                                                        @csrf
                                                        <div class="col-md-2">
                                                            <label class="form-label small">Date paiement</label>
                                                            <input type="date" name="date_paiement" class="form-control form-control-sm" value="{{ now()->format('Y-m-d') }}" required>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="form-label small">Montant</label>
                                                            <input type="number" step="0.01" min="0.01" max="{{ $resteAPayer }}" name="montant" class="form-control form-control-sm" value="{{ $resteAPayer }}" required>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="form-label small">Mode</label>
                                                            <select name="mode_paiement" class="form-select form-select-sm" required>
                                                                <option value="virement">Virement</option>
                                                                <option value="cheque">Chèque</option>
                                                                <option value="especes">Espèces</option>
                                                                <option value="autre">Autre</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="form-label small">Caisse</label>
                                                            <select name="caisse_id" class="form-select form-select-sm">
                                                                <option value="">Sans caisse</option>
                                                                @foreach($caisses as $caisse)
                                                                    <option value="{{ $caisse->id }}">{{ $caisse->nom }} ({{ number_format((float) $caisse->solde_actuel, 0, ',', ' ') }})</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="form-label small">Référence</label>
                                                            <input type="text" name="reference_paiement" class="form-control form-control-sm">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="form-label small">Notes</label>
                                                            <input type="text" name="notes" class="form-control form-control-sm">
                                                        </div>
                                                        <div class="col-12 d-flex justify-content-end">
                                                            <button type="submit" class="btn btn-sm btn-warning"><i class="fas fa-money-bill-wave me-1"></i>Enregistrer un paiement</button>
                                                        </div>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white"><strong>Informations générales</strong></div>
                <div class="card-body small">
                    <div class="mb-2"><strong>Fournisseur:</strong> {{ $achat->fournisseur->raison_sociale ?? '-' }}</div>
                    <div class="mb-2"><strong>Type d'achat:</strong> {{ $achat->type_achat ?? '-' }}</div>
                    <div class="mb-2"><strong>Service concerné:</strong> {{ $achat->service_concerne ?? '-' }}</div>
                    <div class="mb-2"><strong>Date achat:</strong> {{ optional($achat->date_commande)->format('d/m/Y') }}</div>
                    <div class="mb-2"><strong>Compte comptable:</strong> {{ $achat->compteComptable->numero ?? $achat->compteComptable->numero_compte ?? $achat->compteComptable->code ?? '-' }} - {{ $achat->compteComptable->intitule ?? $achat->compteComptable->libelle ?? '-' }}</div>
                    <div class="mb-2"><strong>Mode paiement:</strong> {{ $achat->mode_paiement ?? '-' }}</div>
                    <div class="mb-2"><strong>Conditions:</strong> {{ $achat->conditions_paiement ?? '-' }}</div>
                    <div class="mb-2"><strong>Validé par:</strong> {{ $achat->validatedBy->name ?? '-' }}</div>
                    <div class="mb-2"><strong>Date validation:</strong> {{ optional($achat->date_validation_achat)->format('d/m/Y H:i') ?? '-' }}</div>
                    @if($achat->notes)
                        <hr>
                        <div><strong>Notes:</strong><br>{{ $achat->notes }}</div>
                    @endif
                </div>
            </div>

            @if($achat->expense)
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white"><strong>Suivi comptable</strong></div>
                    <div class="card-body small">
                        <div class="mb-2"><strong>Référence dépense:</strong> {{ $achat->expense->reference }}</div>
                        <div class="mb-2"><strong>Statut:</strong> {{ $achat->expense->statut }}</div>
                        <div class="mb-2"><strong>Date approbation:</strong> {{ optional($achat->expense->date_approbation)->format('d/m/Y H:i') ?? '-' }}</div>
                        @if($achat->expense->depenseCaisse)
                            <div class="mb-2"><strong>Référence décaissement:</strong> {{ $achat->expense->depenseCaisse->reference }}</div>
                            <div class="mb-2"><strong>Caisse:</strong> {{ $achat->expense->depenseCaisse->caisse->nom ?? '-' }}</div>
                        @endif
                    </div>
                </div>
            @endif

            @if($achat->factures->isNotEmpty())
                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-header bg-white"><strong>Paiements enregistrés</strong></div>
                    <div class="card-body small">
                        <ul class="list-unstyled mb-0">
                            @php $hasPayments = false; @endphp
                            @foreach($achat->factures as $facture)
                                @foreach($facture->paiements as $paiement)
                                    @php $hasPayments = true; @endphp
                                    <li class="mb-3 border-bottom pb-2">
                                        <div class="fw-semibold">{{ $facture->numero_facture }} - {{ number_format((float) $paiement->montant, 0, ',', ' ') }} FCFA</div>
                                        <div>{{ optional($paiement->date_paiement)->format('d/m/Y') }} | {{ $paiement->mode_paiement }}</div>
                                        <div class="text-muted">{{ $paiement->reference_paiement ?? $paiement->reference }}</div>
                                        @if($paiement->depenseCaisse)
                                            <div class="text-success">Décaissement: {{ $paiement->depenseCaisse->reference }}</div>
                                        @endif
                                    </li>
                                @endforeach
                            @endforeach
                            @if(!$hasPayments)
                                <li class="text-muted">Aucun paiement fournisseur enregistré.</li>
                            @endif
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection