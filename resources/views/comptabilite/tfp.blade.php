@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-purple text-white d-flex justify-content-between align-items-center"
                     style="background-color: #6f42c1 !important;">
                    <h4 class="mb-0">
                        <i class="fas fa-graduation-cap me-2"></i>
                        TFP & Taxe d'Apprentissage — FDFP Côte d'Ivoire
                    </h4>
                    <div>
                        <span class="badge bg-white text-dark me-1">TFP 1,2%</span>
                        <span class="badge bg-warning text-dark">Apprentissage 0,4%</span>
                    </div>
                </div>

                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Base légale FDFP :</strong>
                        <ul class="mb-0 mt-1">
                            <li><strong>TFP</strong> (Taxe de Formation Professionnelle Continue) : <strong>1,2%</strong> de la masse salariale brute</li>
                            <li><strong>Taxe d'Apprentissage</strong> : <strong>0,4%</strong> de la masse salariale brute</li>
                            <li>Total : <strong>1,6%</strong> de la masse salariale — versé trimestriellement au FDFP</li>
                        </ul>
                    </div>

                    <!-- Sélecteur de période -->
                    <form method="GET" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Période</label>
                            <input type="month" name="periode" class="form-control"
                                   value="{{ $periode }}" max="{{ now()->format('Y-m') }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-sync me-1"></i>Actualiser
                            </button>
                        </div>
                    </form>

                    <!-- Résultats automatiques BDD -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card border-secondary">
                                <div class="card-body text-center">
                                    <div class="text-muted small">Masse salariale brute</div>
                                    <div class="fw-bold fs-5">{{ number_format($masse_salariale, 0, ',', ' ') }} FCFA</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <div class="text-muted small">TFP (1,2%)</div>
                                    <div class="fw-bold fs-5 text-primary">{{ number_format($tfp, 0, ',', ' ') }} FCFA</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <div class="text-muted small">Taxe d'Apprentissage (0,4%)</div>
                                    <div class="fw-bold fs-5 text-warning">{{ number_format($taxe_apprentissage, 0, ',', ' ') }} FCFA</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-danger bg-danger text-white">
                                <div class="card-body text-center">
                                    <div class="small">TOTAL À VERSER AU FDFP</div>
                                    <div class="fw-bold fs-4">{{ number_format($total, 0, ',', ' ') }} FCFA</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Calcul manuel -->
                    <h5 class="text-primary"><i class="fas fa-edit me-2"></i>Calcul manuel / simulation</h5>
                    <form id="tfp-form">
                        @csrf
                        <input type="hidden" name="periode" value="{{ $periode }}">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Masse salariale brute du mois (FCFA)</label>
                                <input type="number" name="masse_salariale" id="masse_input"
                                       class="form-control" value="{{ $masse_salariale }}" min="0" step="1">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-calculator me-1"></i>Calculer
                                </button>
                            </div>
                        </div>
                    </form>

                    <div id="tfp-resultats" style="display:none;" class="mt-3">
                        <div class="alert alert-success" id="tfp-message"></div>
                    </div>

                    <!-- Tableau récapitulatif -->
                    <hr>
                    <h5 class="text-primary"><i class="fas fa-table me-2"></i>Récapitulatif — {{ \Carbon\Carbon::createFromFormat('Y-m', $periode)->translatedFormat('F Y') }}</h5>
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th class="table-light" style="width:60%">Masse salariale brute</th>
                                <td class="text-end fw-bold">{{ number_format($masse_salariale, 0, ',', ' ') }} FCFA</td>
                            </tr>
                            <tr>
                                <th class="table-light">TFP — 1,2% (FDFP)</th>
                                <td class="text-end text-primary fw-bold">{{ number_format($tfp, 0, ',', ' ') }} FCFA</td>
                            </tr>
                            <tr>
                                <th class="table-light">Taxe d'Apprentissage — 0,4% (FDFP)</th>
                                <td class="text-end text-warning fw-bold">{{ number_format($taxe_apprentissage, 0, ',', ' ') }} FCFA</td>
                            </tr>
                            <tr class="table-danger fw-bold">
                                <th>TOTAL À VERSER AU FDFP</th>
                                <td class="text-end">{{ number_format($total, 0, ',', ' ') }} FCFA</td>
                            </tr>
                            <tr>
                                <th class="table-light">Date limite</th>
                                <td>{{ $fin->copy()->addDays(15)->format('d/m/Y') }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Rappel :</strong> La TFP et la Taxe d'Apprentissage sont versées trimestriellement au
                        <strong>FDFP (Fonds de Développement de la Formation Professionnelle)</strong>, Abidjan.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('tfp-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const masse = parseFloat(document.getElementById('masse_input').value) || 0;
    const periode = document.querySelector('input[name=periode]').value;
    fetch('{{ route("comptabilite.tfp.calculer") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify({ periode, masse_salariale: masse })
    })
    .then(r => r.json())
    .then(d => {
        const fmt = n => new Intl.NumberFormat('fr-FR').format(Math.round(n)) + ' FCFA';
        document.getElementById('tfp-message').innerHTML =
            `Masse salariale : ${fmt(d.masse_salariale)}<br>` +
            `TFP (${d.taux_tfp}%) : <strong>${fmt(d.tfp)}</strong><br>` +
            `Taxe d'Apprentissage (${d.taux_apprentissage}%) : <strong>${fmt(d.taxe_apprentissage)}</strong><br>` +
            `<strong>Total FDFP : ${fmt(d.total)}</strong> — Échéance : ${d.echeance}`;
        document.getElementById('tfp-resultats').style.display = 'block';
    })
    .catch(err => alert('Erreur : ' + err));
});
</script>
@endpush
@endsection
