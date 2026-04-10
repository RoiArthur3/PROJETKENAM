@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-building me-2"></i>
                        Impôt sur les Sociétés (IS) — 25% — CGI Art. 63
                    </h4>
                    <span class="badge bg-white text-danger fs-6">Taux IS : 25%</span>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Base légale :</strong> CGI Côte d'Ivoire Art. 63 — IS = 25% du bénéfice imposable.
                        Les acomptes trimestriels sont versés en mars, juin, septembre et décembre (Art. 79).
                    </div>

                    <form id="is-form">
                        @csrf
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-danger"><i class="fas fa-calendar-alt me-2"></i>Exercice fiscal</h5>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Année fiscale</label>
                                <select name="annee" class="form-select" required>
                                    @for($y = now()->year; $y >= now()->year - 5; $y--)
                                        <option value="{{ $y }}" {{ $annee == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Bénéfice brut avant IS (FCFA)</label>
                                <input type="number" name="benefice_brut" class="form-control"
                                       value="{{ number_format($benefice, 0, '.', '') }}" min="0" step="1" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Charges non déductibles (FCFA)</label>
                                <input type="number" name="charges_non_deductibles" class="form-control" value="0" min="0">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Crédits d'impôt / retenues précomptées (FCFA)</label>
                                <input type="number" name="credits_is" class="form-control" value="0" min="0">
                            </div>
                        </div>

                        <div class="d-flex gap-2 mb-4">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-calculator me-2"></i>Calculer l'IS
                            </button>
                        </div>
                    </form>

                    <!-- Résultats automatiques -->
                    <div id="resultats-is" class="mt-4" style="display:none;">
                        <hr>
                        <h5 class="text-danger"><i class="fas fa-chart-pie me-2"></i>Résultat du calcul</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card border-secondary">
                                    <div class="card-body text-center">
                                        <div class="text-muted small">Bénéfice imposable</div>
                                        <div class="fw-bold fs-5" id="r-benefice">—</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-warning">
                                    <div class="card-body text-center">
                                        <div class="text-muted small">IS brut (25%)</div>
                                        <div class="fw-bold fs-5 text-warning" id="r-is-brut">—</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-info">
                                    <div class="card-body text-center">
                                        <div class="text-muted small">Crédits déduits</div>
                                        <div class="fw-bold fs-5 text-info" id="r-credits">—</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-danger bg-danger text-white">
                                    <div class="card-body text-center">
                                        <div class="small">IS NET À PAYER</div>
                                        <div class="fw-bold fs-4" id="r-is-net">—</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h5 class="text-danger mt-4"><i class="fas fa-calendar-check me-2"></i>Acomptes trimestriels</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-danger">
                                    <tr>
                                        <th>Échéance</th>
                                        <th>Quote-part</th>
                                        <th class="text-end">Montant à verser (FCFA)</th>
                                    </tr>
                                </thead>
                                <tbody id="acomptes-tbody"></tbody>
                                <tfoot>
                                    <tr class="table-dark fw-bold">
                                        <td colspan="2">TOTAL IS ANNUEL</td>
                                        <td class="text-end" id="r-total">—</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Résultats calculés côté PHP (exercice courant) -->
                    @if($benefice > 0)
                    <div class="mt-4">
                        <hr>
                        <h5 class="text-danger"><i class="fas fa-database me-2"></i>Données automatiques — Exercice {{ $annee }}</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card border-success">
                                    <div class="card-body text-center">
                                        <div class="text-muted small">Bénéfice calculé (BDD)</div>
                                        <div class="fw-bold fs-5 text-success">{{ number_format($benefice, 0, ',', ' ') }} FCFA</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-danger">
                                    <div class="card-body text-center">
                                        <div class="text-muted small">IS calculé (25%)</div>
                                        <div class="fw-bold fs-5 text-danger">{{ number_format($is, 0, ',', ' ') }} FCFA</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-warning">
                                    <div class="card-body text-center">
                                        <div class="text-muted small">Acompte trimestriel</div>
                                        <div class="fw-bold fs-5 text-warning">{{ number_format($is / 4, 0, ',', ' ') }} FCFA</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h6 class="mt-3">Calendrier d'acomptes :</h6>
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr><th>Échéance</th><th class="text-end">Montant</th></tr>
                            </thead>
                            <tbody>
                                @foreach($acomptes as $a)
                                <tr>
                                    <td>{{ $a['trimestre'] }}</td>
                                    <td class="text-end fw-bold">{{ number_format($a['montant'], 0, ',', ' ') }} FCFA</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('is-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const data = {
        annee:                   formData.get('annee'),
        benefice_brut:           formData.get('benefice_brut') || 0,
        charges_non_deductibles: formData.get('charges_non_deductibles') || 0,
        credits_is:              formData.get('credits_is') || 0,
        _token:                  document.querySelector('input[name=_csrf]')?.value || '{{ csrf_token() }}'
    };

    fetch('{{ route("comptabilite.impot-societes.calculer") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(d => {
        const fmt = n => new Intl.NumberFormat('fr-FR').format(Math.round(n)) + ' FCFA';
        document.getElementById('r-benefice').textContent  = fmt(d.benefice_imposable);
        document.getElementById('r-is-brut').textContent   = fmt(d.is_brut);
        document.getElementById('r-credits').textContent   = fmt(d.credits_is);
        document.getElementById('r-is-net').textContent    = fmt(d.is_net);
        document.getElementById('r-total').textContent     = fmt(d.is_net);
        const tbody = document.getElementById('acomptes-tbody');
        tbody.innerHTML = '';
        d.acomptes.forEach(a => {
            tbody.innerHTML += `<tr><td>${a.trimestre}</td><td class="text-center">${a.taux}</td><td class="text-end fw-bold">${fmt(a.montant)}</td></tr>`;
        });
        document.getElementById('resultats-is').style.display = 'block';
    })
    .catch(err => alert('Erreur de calcul : ' + err));
});
</script>
@endpush
@endsection
