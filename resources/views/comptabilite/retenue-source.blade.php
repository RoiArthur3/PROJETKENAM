@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-warning d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 text-dark">
                        <i class="fas fa-hand-holding-usd me-2"></i>
                        Retenue à la Source (RAS) — CGI Art. 165-180
                    </h4>
                    <span class="badge bg-dark">DGI Côte d'Ivoire</span>
                </div>

                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Base légale CGI Côte d'Ivoire :</strong>
                        <ul class="mb-0 mt-1">
                            <li><strong>Services locaux (résidents)</strong> : 5% — CGI Art. 165</li>
                            <li><strong>Services non-résidents / étrangers</strong> : 15% — CGI Art. 166</li>
                            <li><strong>Importations</strong> : 5% — CGI Art. 167</li>
                            <li><strong>Royalties</strong> : 10% — CGI Art. 168</li>
                            <li><strong>Dividendes</strong> : 10% — CGI Art. 169</li>
                        </ul>
                    </div>

                    <!-- Calculateur -->
                    <h5 class="text-dark"><i class="fas fa-calculator me-2"></i>Calculer une retenue</h5>
                    <form id="ras-form">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Montant HT (FCFA)</label>
                                <input type="number" name="montant_ht" class="form-control" min="0" step="1" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Type de prestation</label>
                                <select name="type_prestation" class="form-select" required>
                                    <option value="">Sélectionner...</option>
                                    <option value="service_local">Service local (résident) — 5%</option>
                                    <option value="service_non_resident">Service non-résident — 15%</option>
                                    <option value="importation">Importation — 5%</option>
                                    <option value="royalties">Royalties — 10%</option>
                                    <option value="dividendes">Dividendes — 10%</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Bénéficiaire résident</label>
                                <div class="mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="beneficiaire_resident" value="1" checked>
                                        <label class="form-check-label">Oui</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="beneficiaire_resident" value="0">
                                        <label class="form-check-label">Non</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Assujetti TVA</label>
                                <div class="mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="assujetti_tva" value="1" checked>
                                        <label class="form-check-label">Oui</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="assujetti_tva" value="0">
                                        <label class="form-check-label">Non</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Fournisseur / Prestataire</label>
                                <input type="text" name="fournisseur" class="form-control" placeholder="Nom du fournisseur...">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nature de la prestation</label>
                                <input type="text" name="nature" class="form-control" placeholder="Ex: Maintenance informatique...">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-warning w-100 text-dark fw-bold">
                                    <i class="fas fa-calculator me-1"></i>Calculer
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Résultats -->
                    <div id="ras-resultats" style="display:none;" class="mt-3">
                        <hr>
                        <h5 class="text-warning"><i class="fas fa-receipt me-2"></i>Résultat RAS</h5>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="card border-secondary">
                                    <div class="card-body text-center p-2">
                                        <div class="text-muted small">Montant HT</div>
                                        <div class="fw-bold" id="r-ht">—</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="card border-info">
                                    <div class="card-body text-center p-2">
                                        <div class="text-muted small">TVA (18%)</div>
                                        <div class="fw-bold text-info" id="r-tva">—</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="card border-secondary">
                                    <div class="card-body text-center p-2">
                                        <div class="text-muted small">Montant TTC</div>
                                        <div class="fw-bold" id="r-ttc">—</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="card border-warning">
                                    <div class="card-body text-center p-2">
                                        <div class="text-muted small">Taux RAS</div>
                                        <div class="fw-bold text-warning" id="r-taux">—</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="card border-danger">
                                    <div class="card-body text-center p-2">
                                        <div class="text-muted small">Retenue à verser DGI</div>
                                        <div class="fw-bold text-danger" id="r-ras">—</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="card border-success bg-success text-white">
                                    <div class="card-body text-center p-2">
                                        <div class="small">Net à payer fournisseur</div>
                                        <div class="fw-bold" id="r-net">—</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 text-muted small" id="r-reference"></div>
                    </div>

                    <div class="alert alert-warning mt-4">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Rappel :</strong> La retenue à la source est prélevée sur le paiement et reversée à la DGI
                        avant le 15 du mois suivant. Elle constitue un crédit d'impôt pour le prestataire résident.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('ras-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    fetch('{{ route("comptabilite.retenue-source.calculer") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify({
            montant_ht:            parseFloat(fd.get('montant_ht')),
            type_prestation:       fd.get('type_prestation'),
            beneficiaire_resident: fd.get('beneficiaire_resident') === '1',
            assujetti_tva:         fd.get('assujetti_tva') === '1',
        })
    })
    .then(r => r.json())
    .then(d => {
        const fmt = n => new Intl.NumberFormat('fr-FR').format(Math.round(n)) + ' FCFA';
        document.getElementById('r-ht').textContent    = fmt(d.montant_ht);
        document.getElementById('r-tva').textContent   = fmt(d.tva);
        document.getElementById('r-ttc').textContent   = fmt(d.montant_ttc);
        document.getElementById('r-taux').textContent  = d.taux_ras + '%';
        document.getElementById('r-ras').textContent   = fmt(d.retenue_source);
        document.getElementById('r-net').textContent   = fmt(d.montant_a_payer);
        document.getElementById('r-reference').textContent = 'Base légale : ' + d.reference_cgi;
        document.getElementById('ras-resultats').style.display = 'block';
    })
    .catch(err => alert('Erreur : ' + err));
});
</script>
@endpush
@endsection
