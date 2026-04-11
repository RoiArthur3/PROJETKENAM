@extends('layouts.app')

@section('title', 'Estimation de Cout - Détail')

@section('content')
<div class="project-page">
    <div class="project-hero card border-0 shadow-sm mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <p class="text-uppercase small fw-bold text-primary mb-2">Estimation de Cout</p>
                    <h1 class="h3 mb-2">{{ $project->nom }}</h1>
                    <p class="text-muted mb-0">{{ $project->description }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    @if($project->canValidate())
                        <form action="{{ route('projets.validateProject', $project->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary">Valider</button>
                        </form>
                    @endif
                    @if($project->canStart())
                        <form action="{{ route('projets.startProject', $project->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">Demarrer</button>
                        </form>
                    @endif
                    @if($project->canClose())
                        <form action="{{ route('projets.closeProject', $project->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning text-dark">Cloturer</button>
                        </form>
                    @endif
                    <a href="{{ route('projets.edit', $project->id) }}" class="btn btn-outline-dark">Modifier</a>
                </div>
            </div>
        </div>
    </div>

    @if(!empty($kpis['alerts']))
        <div class="mb-4">
            @foreach($kpis['alerts'] as $alert)
                <div class="alert {{ $alert['type'] === 'danger' ? 'alert-danger' : 'alert-warning' }} border-0 shadow-sm mb-2">
                    <strong>{{ $alert['title'] }}:</strong> {{ $alert['message'] }}
                </div>
            @endforeach
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="kpi-card card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="kpi-label">Operations</div>
                    <div class="kpi-value">{{ $kpis['operationsCount'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="kpi-card card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="kpi-label">Ressources actives</div>
                    <div class="kpi-value">{{ array_sum($kpis['resourcesCount']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="kpi-card card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="kpi-label">Depenses</div>
                    <div class="kpi-value">{{ number_format($kpis['expensesTotal'], 0, ',', ' ') }}</div>
                    <div class="kpi-unit">FCFA</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="kpi-card card h-100 border-0 shadow-sm {{ $kpis['profitability']['profit'] >= 0 ? 'kpi-positive' : 'kpi-negative' }}">
                <div class="card-body">
                    <div class="kpi-label">Rentabilite</div>
                    <div class="kpi-value">{{ number_format($kpis['profitability']['profit'], 0, ',', ' ') }}</div>
                    <div class="kpi-unit">FCFA</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h2 class="h5 mb-0">Etat et avancement</h2>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="info-grid">
                        <div>
                            <span class="label">Statut</span>
                            <span class="value">
                                <span class="badge text-bg-secondary">{{ ucfirst($project->statut) }}</span>
                            </span>
                        </div>
                        <div>
                            <span class="label">Responsable</span>
                            <span class="value">{{ $project->responsable->nom ?? ($project->responsable->name ?? 'Non assigne') }}</span>
                        </div>
                        <div>
                            <span class="label">Client</span>
                            <span class="value">{{ $project->client->nom ?? 'Non assigne' }}</span>
                        </div>
                        <div>
                            <span class="label">Type</span>
                            <span class="value">{{ ucfirst(str_replace('_', ' ', $project->type)) }}</span>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold">Progression globale</span>
                        <span class="fw-bold">{{ $kpis['progress']['overall_progress'] }}%</span>
                    </div>
                    <div class="progress progress-modern mb-3">
                        <div class="progress-bar" role="progressbar" style="width: {{ $kpis['progress']['overall_progress'] }}%"></div>
                    </div>

                    <form action="{{ route('projets.updateProgress', $project->id) }}" method="POST" class="row g-2">
                        @csrf
                        <div class="col-8 col-md-9">
                            <input type="number" name="pourcentage_avancement" min="0" max="100" value="{{ $project->pourcentage_avancement }}" class="form-control" placeholder="Ex: 65">
                        </div>
                        <div class="col-4 col-md-3">
                            <button type="submit" class="btn btn-primary w-100">Maj</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h2 class="h5 mb-0">Cout estimé basé sur les pointages engins</h2>
                </div>
                <div class="card-body px-4 pb-4">
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span>Cout estimé (prévisionnel)</span>
                            <strong>{{ number_format($kpis['budgetAnalysis']['budget_estime'], 0, ',', ' ') }} FCFA</strong>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span>Cout réel (pointages engins)</span>
                            <strong>{{ number_format($kpis['budgetAnalysis']['budget_reel'], 0, ',', ' ') }} FCFA</strong>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span>Ecart</span>
                            <strong class="{{ $kpis['budgetAnalysis']['ecart'] > 0 ? 'text-danger' : 'text-success' }}">
                                {{ $kpis['budgetAnalysis']['ecart'] > 0 ? '+' : '' }}{{ number_format($kpis['budgetAnalysis']['ecart'], 0, ',', ' ') }} FCFA
                            </strong>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span>Taux d'utilisation</span>
                            <strong>{{ $kpis['budgetAnalysis']['taux_utilisation'] }}%</strong>
                        </li>
                    </ul>

                    <div class="small text-muted mb-2">Depenses par type</div>
                    @forelse($kpis['expensesByType'] as $type => $amount)
                        <div class="d-flex justify-content-between align-items-center border rounded-3 px-3 py-2 mb-2">
                            <span>{{ ucfirst(str_replace('_', ' ', $type)) }}</span>
                            <strong>{{ number_format($amount, 0, ',', ' ') }} FCFA</strong>
                        </div>
                    @empty
                        <div class="alert alert-light border">Aucune depense enregistree.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>


    <div class="mb-4">
        <a href="{{ route('projets.pointage_report', $project->id) }}" class="btn btn-outline-primary">
            <i class="fas fa-table me-1"></i> Rapport de pointage engins (filtrable)
        </a>
    </div>

    @if(!empty($kpis['milestonesStatus']))
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h2 class="h5 mb-0">Milestones</h2>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="timeline-list">
                    @foreach($kpis['milestonesStatus'] as $milestone)
                        <div class="timeline-item {{ $milestone['is_retarded'] ? 'is-late' : '' }}">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between flex-wrap gap-2">
                                    <h6 class="mb-1">{{ $milestone['title'] }}</h6>
                                    <span class="badge {{ $milestone['status'] === 'completee' ? 'text-bg-success' : ($milestone['status'] === 'retardee' ? 'text-bg-danger' : 'text-bg-secondary') }}">
                                        {{ ucfirst($milestone['status']) }}
                                    </span>
                                </div>
                                <div class="text-muted small">
                                    Prevue: {{ optional($milestone['planned_date'])->format('d/m/Y') ?? '-' }}
                                    @if($milestone['actual_date']) | Realisee: {{ optional($milestone['actual_date'])->format('d/m/Y') }} @endif
                                </div>
                                <div class="progress mt-2" style="height: 8px;">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $milestone['completion'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
            <h2 class="h5 mb-0">Avances du projet</h2>
            <span class="badge text-bg-light">
                {{ $project->avances->count() }} avance(s)
            </span>
        </div>
        <div class="card-body px-4 pb-4">
            @php
                $totalAvances = (float) $project->avances->sum('montant');
            @endphp
            <div class="d-flex justify-content-between align-items-center border rounded-3 px-3 py-2 mb-3 bg-light">
                <span class="fw-semibold">Total des avances enregistrées</span>
                <strong>{{ number_format($totalAvances, 0, ',', ' ') }} FCFA</strong>
            </div>

            @if($project->avances->isEmpty())
                <div class="alert alert-light border mb-0">Aucune avance enregistrée pour ce projet.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Référence</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th class="text-end">Montant</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($project->avances as $avance)
                                <tr>
                                    <td>{{ optional($avance->date_encaissement)->format('d/m/Y') }}</td>
                                    <td>{{ $avance->reference }}</td>
                                    <td>{{ ucfirst($avance->type_encaissement) }}</td>
                                    <td>{{ $avance->description }}</td>
                                    <td class="text-end fw-bold">{{ number_format($avance->montant, 0, ',', ' ') }} FCFA</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <a href="{{ route('projets.index') }}" class="btn btn-outline-secondary">Retour a la liste</a>
        <a href="{{ route('projets.edit', $project->id) }}" class="btn btn-dark">Modifier ce projet</a>
    </div>
</div>

<style>
.project-page {
    padding: 1rem;
}
.project-hero {
    background: linear-gradient(135deg, #f5fbff 0%, #eef8f3 100%);
}
.kpi-card {
    border-radius: 14px;
}
.kpi-positive {
    background: linear-gradient(180deg, #f2fff8 0%, #ffffff 100%);
}
.kpi-negative {
    background: linear-gradient(180deg, #fff5f5 0%, #ffffff 100%);
}
.kpi-label {
    color: #5f6775;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.78rem;
    letter-spacing: 0.5px;
}
.kpi-value {
    font-size: 1.7rem;
    font-weight: 800;
    line-height: 1.1;
    margin-top: 0.4rem;
}
.kpi-unit {
    color: #6c757d;
    font-size: 0.82rem;
}
.info-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.9rem;
}
.info-grid .label {
    display: block;
    font-size: 0.78rem;
    text-transform: uppercase;
    color: #6c757d;
    margin-bottom: 0.2rem;
    font-weight: 700;
}
.info-grid .value {
    font-weight: 600;
}
.progress-modern {
    height: 10px;
    border-radius: 999px;
    background: #e9ecef;
}
.progress-modern .progress-bar {
    border-radius: 999px;
    background: linear-gradient(90deg, #0ea5e9 0%, #10b981 100%);
}
.timeline-list {
    border-left: 2px solid #e8edf2;
    padding-left: 1rem;
}
.timeline-item {
    position: relative;
    margin-bottom: 1rem;
}
.timeline-item:last-child {
    margin-bottom: 0;
}
.timeline-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #0d6efd;
    position: absolute;
    left: -22px;
    top: 7px;
}
.timeline-item.is-late .timeline-dot {
    background: #dc3545;
}
.timeline-content {
    border: 1px solid #edf1f4;
    border-radius: 12px;
    padding: 0.8rem 0.9rem;
    background: #fff;
}
@media (max-width: 767.98px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection