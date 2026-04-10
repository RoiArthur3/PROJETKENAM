@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-receipt me-2"></i>
                        Déclaration TVA Mensuelle — DGI Côte d'Ivoire
                    </h4>
                    <span class="badge bg-white text-success fs-6">TVA : 18% (CGI Art. 339)</span>
                </div>

                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Formule :</strong> TVA collectée (sur ventes) − TVA déductible (sur achats) = TVA nette à reverser à la DGI.
                        Déclaration mensuelle via le formulaire 301-C ou équivalent DGI.
                    </div>

                    <!-- Sélecteur de période -->
                    <form method="GET" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Période (mois/année)</label>
                            <input type="month" name="periode" class="form-control"
                                   value="{{ $periode }}" max="{{ now()->format('Y-m') }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-sync me-1"></i>Actualiser
                            </button>
                        </div>
                    </form>

                    <!-- Résumé automatique -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <div class="text-muted small">TVA COLLECTÉE (sur ventes)</div>
                                    <div class="fw-bold fs-4 text-success">{{ number_format($tva_collectee, 0, ',', ' ') }} FCFA</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <div class="text-muted small">TVA DÉDUCTIBLE (sur achats)</div>
                                    <div class="fw-bold fs-4 text-warning">{{ number_format($tva_deductible, 0, ',', ' ') }} FCFA</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            @if($solde_a_reverser > 0)
                            <div class="card border-danger bg-danger text-white">
                                <div class="card-body text-center">
                                    <div class="small">TVA À REVERSER À LA DGI</div>
                                    <div class="fw-bold fs-4">{{ number_format($solde_a_reverser, 0, ',', ' ') }} FCFA</div>
                                </div>
                            </div>
                            @else
                            <div class="card border-info bg-info text-white">
                                <div class="card-body text-center">
                                    <div class="small">CRÉDIT DE TVA (report)</div>
                                    <div class="fw-bold fs-4">{{ number_format($credit_tva, 0, ',', ' ') }} FCFA</div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Calcul manuel / correctif -->
                    <h5 class="text-success"><i class="fas fa-edit me-2"></i>Ajustement manuel</h5>
                    <form id="tva-form">
                        @csrf
                        <input type="hidden" name="periode" value="{{ $periode }}">
                        <div class="row mb-3">
                            <div class="col-md-5">
                                <label class="form-label">TVA collectée corrigée (FCFA)</label>
                                <input type="number" name="tva_collectee" class="form-control"
                                       value="{{ $tva_collectee }}" min="0" step="0.01">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">TVA déductible corrigée (FCFA)</label>
                                <input type="number" name="tva_deductible" class="form-control"
                                       value="{{ $tva_deductible }}" min="0" step="0.01">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-outline-success w-100">
                                    <i class="fas fa-calculator me-1"></i>Recalculer
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Résultats recalculés -->
                    <div id="tva-resultats" class="mt-3" style="display:none;">
                        <div class="alert alert-success" id="tva-message"></div>
                    </div>

                    <!-- Tableau récapitulatif -->
                    <hr>
                    <h5 class="text-success"><i class="fas fa-table me-2"></i>Déclaration TVA — {{ \Carbon\Carbon::createFromFormat('Y-m', $periode)->translatedFormat('F Y') }}</h5>
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th class="table-light" style="width:60%">Chiffre d'affaires HT du mois</th>
                                <td class="text-end fw-bold">{{ number_format($tva_collectee > 0 ? $tva_collectee / 0.18 : 0, 0, ',', ' ') }} FCFA</td>
                            </tr>
                            <tr>
                                <th class="table-light">TVA collectée (18%)</th>
                                <td class="text-end fw-bold text-success">{{ number_format($tva_collectee, 0, ',', ' ') }} FCFA</td>
                            </tr>
                            <tr>
                                <th class="table-light">TVA déductible sur achats</th>
                                <td class="text-end fw-bold text-warning">{{ number_format($tva_deductible, 0, ',', ' ') }} FCFA</td>
                            </tr>
                            <tr class="{{ $solde_a_reverser > 0 ? 'table-danger' : 'table-info' }} fw-bold">
                                <th>{{ $solde_a_reverser > 0 ? 'TVA NETTE À REVERSER' : 'CRÉDIT DE TVA À REPORTER' }}</th>
                                <td class="text-end">{{ number_format($solde_a_reverser > 0 ? $solde_a_reverser : $credit_tva, 0, ',', ' ') }} FCFA</td>
                            </tr>
                            <tr>
                                <th class="table-light">Date limite de paiement</th>
                                <td>{{ $fin->copy()->addDays(15)->format('d/m/Y') }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Rappel :</strong> La déclaration TVA est à déposer avant le 15 du mois suivant auprès de la DGI
                        (Direction Générale des Impôts, Abidjan) via le formulaire 301-C ou l'e-service DGI CIV.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('tva-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    fetch('{{ route("comptabilite.tva-declarative.calculer") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify({
            periode:        fd.get('periode'),
            tva_collectee:  fd.get('tva_collectee') || undefined,
            tva_deductible: fd.get('tva_deductible') || undefined,
        })
    })
    .then(r => r.json())
    .then(d => {
        const fmt = n => new Intl.NumberFormat('fr-FR').format(Math.round(n)) + ' FCFA';
        const msg = d.a_reverser_dgi > 0
            ? `TVA à reverser à la DGI : <strong>${fmt(d.a_reverser_dgi)}</strong>`
            : `Crédit de TVA à reporter : <strong>${fmt(d.credit_reporte)}</strong>`;
        document.getElementById('tva-message').innerHTML =
            `TVA collectée : ${fmt(d.tva_collectee)} — TVA déductible : ${fmt(d.tva_deductible)}<br>${msg}<br>Échéance paiement : ${d.echeance_paiement}`;
        document.getElementById('tva-resultats').style.display = 'block';
    })
    .catch(err => alert('Erreur : ' + err));
});
</script>
@endpush
@endsection
