@extends('layouts.app')

@section('title', 'RH - Bulletin de Paie | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-invoice-dollar mr-2 text-primary"></i>Bulletin de Paie
            </h1>
            <p class="text-muted">Détail du bulletin pour {{ $paie->user->name ?? '' }} - {{ sprintf('%02d/%04d', $paie->mois, $paie->annee) }}</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('rh.paie.index', ['mois' => sprintf('%04d-%02d', $paie->annee, $paie->mois)]) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i>Retour à la paie
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Récapitulatif</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">Salarié</h6>
                            <p class="mb-1 font-weight-bold">{{ $paie->user->name ?? '' }}</p>
                            <p class="mb-1 small text-muted">Poste : {{ $paie->user->role ?? '—' }}</p>
                            <p class="mb-1 small text-muted">ID : {{ $paie->user->id ?? '' }}</p>
                        </div>
                        <div class="col-md-6 text-md-right">
                            <h6 class="text-muted">Période</h6>
                            <p class="mb-1 font-weight-bold">{{ sprintf('%02d/%04d', $paie->mois, $paie->annee) }}</p>
                            <p class="mb-1 small text-muted">Statut :
                                @php
                                    $status = $paie->statut ?? 'Non établi';
                                    $color = $status === 'paye' ? 'success' : ($status === 'en_attente' ? 'warning' : 'secondary');
                                @endphp
                                <span class="badge badge-{{ $color }} ml-1">{{ ucfirst($status) }}</span>
                            </p>
                            <p class="mb-1 small text-muted">Date de paiement : {{ $paie->date_paiement ? $paie->date_paiement->format('d/m/Y') : '—' }}</p>
                        </div>
                    </div>

                    <hr>

                    <h6 class="text-muted">Rémunération</h6>
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-bordered mb-0">
                            <tbody>
                                <tr>
                                    <th style="width: 50%">Salaire de base</th>
                                    <td class="text-right">{{ number_format($paie->salaire_base, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr>
                                    <th>Heures supplémentaires</th>
                                    <td class="text-right">{{ number_format($paie->heures_sup, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr>
                                    <th>Primes</th>
                                    <td class="text-right">{{ number_format($paie->primes, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr class="table-light">
                                    <th>Total brut</th>
                                    <td class="text-right font-weight-bold">{{ number_format($paie->brut, 0, ',', ' ') }} FCFA</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h6 class="text-muted">Cotisations et retenues</h6>
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Libellé</th>
                                    <th class="text-right">Part salariale</th>
                                    <th class="text-right">Part patronale</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>CNPS</td>
                                    <td class="text-right">{{ number_format($paie->cnps_salariale, 0, ',', ' ') }} FCFA</td>
                                    <td class="text-right">{{ number_format($paie->cnps_patronale, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr>
                                    <td>Autres retenues</td>
                                    <td class="text-right">{{ number_format($paie->autres_retenues, 0, ',', ' ') }} FCFA</td>
                                    <td class="text-right">0 FCFA</td>
                                </tr>
                                <tr class="table-light">
                                    <th>Total retenues salariales</th>
                                    <td class="text-right font-weight-bold">{{ number_format($paie->cnps_salariale + $paie->autres_retenues, 0, ',', ' ') }} FCFA</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h6 class="text-muted">Net à payer</h6>
                    <div class="alert alert-success mb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Montant net à payer</span>
                            <span class="h5 mb-0 font-weight-bold">{{ number_format($paie->net_a_payer, 0, ',', ' ') }} FCFA</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('rh.paie.export', ['mois' => sprintf('%04d-%02d', $paie->annee, $paie->mois)]) }}" class="btn btn-outline-info btn-block mb-2">
                        <i class="fas fa-download mr-1"></i>Télécharger les bulletins PDF du mois
                    </a>
                    <p class="small text-muted mb-0">
                        Cette fiche est une vue détaillée. Les exports PDF groupés se font depuis la page principale de paie.
                    </p>
                </div>
            </div>

            @if($paie->commentaires)
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Commentaires</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $paie->commentaires }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
