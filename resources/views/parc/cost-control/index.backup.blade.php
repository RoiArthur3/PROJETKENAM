@extends('layouts.app')

@section('title', 'Cost Control | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-stopwatch me-2 text-info"></i>Cost Control</h1>
            <p class="text-muted mb-0">Temps réel, charges chantier et chiffre d'affaires par mission ou projet</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('materiel.cost-control.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Nouveau pointage
            </a>
            <a href="{{ route('materiel.cost-control.financial-entries.create') }}" class="btn btn-outline-primary">
                <i class="fas fa-wallet me-1"></i>Nouvelle charge / CA
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3 col-lg-2">
            <div class="card shadow-sm border-0 h-100"><div class="card-body"><div class="text-muted small">Pointages</div><div class="h4 mb-0 fw-bold">{{ $summary['total_pointages'] }}</div></div></div>
        </div>
        <div class="col-md-3 col-lg-2">
            <div class="card shadow-sm border-0 h-100"><div class="card-body"><div class="text-muted small">Heures</div><div class="h4 mb-0 fw-bold">{{ number_format($summary['total_hours'], 2, ',', ' ') }}</div></div></div>
        </div>
        <div class="col-md-3 col-lg-2">
            <div class="card shadow-sm border-0 h-100"><div class="card-body"><div class="text-muted small">Jours</div><div class="h4 mb-0 fw-bold">{{ number_format($summary['total_days'], 2, ',', ' ') }}</div></div></div>
        </div>
        <div class="col-md-3 col-lg-2">
            <div class="card shadow-sm border-0 h-100"><div class="card-body"><div class="text-muted small">Charges additionnelles</div><div class="h5 mb-0 fw-bold text-danger">{{ number_format($summary['additional_charges'], 0, ',', ' ') }} FCFA</div></div></div>
        </div>
        <div class="col-md-3 col-lg-2">
            <div class="card shadow-sm border-0 h-100"><div class="card-body"><div class="text-muted small">Coût total réel</div><div class="h5 mb-0 fw-bold text-danger">{{ number_format($summary['total_cost'], 0, ',', ' ') }} FCFA</div></div></div>
        </div>
        <div class="col-md-3 col-lg-2">
            <div class="card shadow-sm border-0 h-100"><div class="card-body"><div class="text-muted small">CA facturé</div><div class="h5 mb-0 fw-bold text-success">{{ number_format($summary['recognized_revenue'], 0, ',', ' ') }} FCFA</div></div></div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100"><div class="card-body"><div class="text-muted small">CA retenu dans la marge</div><div class="h5 mb-0 fw-bold text-success">{{ number_format($summary['total_revenue'], 0, ',', ' ') }} FCFA</div><div class="small text-muted mt-1">Factures si disponibles, sinon pointages</div></div></div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100"><div class="card-body"><div class="text-muted small">Marge</div><div class="h5 mb-0 fw-bold {{ $summary['total_margin'] >= 0 ? 'text-primary' : 'text-danger' }}">{{ number_format($summary['total_margin'], 0, ',', ' ') }} FCFA</div><div class="small text-muted mt-1">{{ $summary['total_entries'] }} écriture(s) financière(s)</div></div></div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-primary">Charges liées au chantier</h6>
                    <span class="small text-muted">Décaissements, avances et saisies manuelles</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @forelse($chargesByCategory as $category => $total)
                            <div class="col-md-4">
                                <div class="border rounded p-3 h-100 bg-light-subtle">
                                    <div class="small text-muted">{{ \App\Models\VehicleFinancialEntry::categories()[$category] ?? $category }}</div>
                                    <div class="fw-bold text-danger mt-1">{{ number_format($total, 0, ',', ' ') }} FCFA</div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-muted">Aucune charge additionnelle enregistrée.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-primary">Ponts inter-modules</h6>
                </div>
                <div class="card-body small text-muted">
                    <div class="mb-2"><strong>Trésorerie</strong> : les décaissements validés peuvent être rattachés comme charges.</div>
                    <div class="mb-2"><strong>Avances</strong> : référence d'avance chantier enregistrable dès maintenant.</div>
                    <div><strong>Facture</strong> : les factures client alimentent le chiffre d'affaires retenu dans la marge.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Mission</label>
                    <select name="vehicle_mission_id" class="form-select">
                        <option value="">Toutes les missions</option>
                        @foreach($missions as $mission)
                            <option value="{{ $mission->id }}" @selected(request('vehicle_mission_id') == $mission->id)>
                                {{ $mission->reference }} - {{ $mission->destination }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Engin</label>
                    <select name="vehicle_id" class="form-select">
                        <option value="">Tous les engins</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" @selected(request('vehicle_id') == $vehicle->id)>
                                {{ $vehicle->immatriculation }} - {{ $vehicle->marque }} {{ $vehicle->modele }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Du</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Au</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button class="btn btn-primary w-100"><i class="fas fa-search"></i></button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-primary">Synthèse par mission</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Mission</th>
                                    <th>Temps</th>
                                    <th>Coût</th>
                                    <th>Revenu</th>
                                    <th class="text-end">Évolution</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($missionSummaries as $mission)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $mission->reference }}</div>
                                            <div class="small text-muted">{{ $mission->vehicle->immatriculation ?? '-' }}</div>
                                        </td>
                                        <td class="small">
                                            <div>{{ number_format($mission->actual_work_hours, 2, ',', ' ') }} h</div>
                                            <div class="text-muted">{{ number_format($mission->actual_work_days, 2, ',', ' ') }} j</div>
                                        </td>
                                        <td>
                                            <div class="text-danger fw-bold">{{ number_format($mission->actual_supplier_cost, 0, ',', ' ') }}</div>
                                            <div class="small text-muted">+{{ number_format($mission->actual_additional_charges, 0, ',', ' ') }} charges</div>
                                        </td>
                                        <td>
                                            <div class="text-success fw-bold">{{ number_format($mission->actual_client_revenue, 0, ',', ' ') }}</div>
                                            <div class="small text-muted">{{ $mission->recognized_revenue > 0 ? 'Facturé' : 'Pointage/prévision' }}</div>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('materiel.missions.show', $mission) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-chart-line me-1"></i>Voir
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center py-4 text-muted">Aucune mission suivie</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-primary">Pointages engins</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Mission / Projet</th>
                                    <th>Engin</th>
                                    <th>Chauffeur</th>
                                    <th>Temps</th>
                                    <th>Marge</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pointages as $pointage)
                                    <tr>
                                        <td>{{ $pointage->date_pointage?->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="fw-bold">{{ $pointage->activity_label }}</div>
                                        </td>
                                        <td>{{ $pointage->vehicle->immatriculation ?? '-' }}</td>
                                        <td>{{ $pointage->driver->name ?? '-' }}</td>
                                        <td>{{ number_format($pointage->quantity, 2, ',', ' ') }} {{ $pointage->unit_type === 'heure' ? 'h' : 'j' }}</td>
                                        <td class="fw-bold {{ $pointage->gross_margin >= 0 ? 'text-primary' : 'text-danger' }}">{{ number_format($pointage->gross_margin, 0, ',', ' ') }}</td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('materiel.cost-control.edit', $pointage) }}" class="btn btn-outline-primary"><i class="fas fa-edit"></i></a>
                                                <form method="POST" action="{{ route('materiel.cost-control.destroy', $pointage) }}" onsubmit="return confirm('Supprimer ce pointage ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-outline-danger"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center py-4 text-muted">Aucun pointage enregistré</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($pointages->hasPages())
                    <div class="card-footer bg-white border-0">
                        {{ $pointages->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-primary">Écritures financières liées</h6>
            <a href="{{ route('materiel.cost-control.financial-entries.create') }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-plus me-1"></i>Ajouter une charge / CA
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Mission / Projet</th>
                            <th>Nature</th>
                            <th>Source</th>
                            <th>Montant</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($financialEntries as $entry)
                            <tr>
                                <td>{{ $entry->entry_date?->format('d/m/Y') }}</td>
                                <td>
                                    <div class="fw-bold">{{ $entry->mission->reference ?? ($entry->operation->titre ?? 'Sans mission') }}</div>
                                    <div class="small text-muted">{{ $entry->label }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold {{ $entry->entry_type === 'charge' ? 'text-danger' : 'text-success' }}">{{ $entry->entry_type === 'charge' ? 'Charge' : 'CA' }}</div>
                                    <div class="small text-muted">{{ $entry->category_label }}</div>
                                </td>
                                <td>
                                    <div>{{ $entry->source_label }}</div>
                                    @if($entry->source_module === 'tresorerie_decaissement' && $entry->decaissement)
                                        <a href="{{ route('tresorerie.decaissements.show', $entry->decaissement->id) }}" class="small">{{ $entry->decaissement->reference }}</a>
                                    @elseif($entry->source_module === 'facture' && $entry->facture)
                                        <a href="{{ route('comptabilite.factures.show', $entry->facture->id) }}" class="small">{{ $entry->external_reference }}</a>
                                    @elseif($entry->external_reference)
                                        <span class="small text-muted">{{ $entry->external_reference }}</span>
                                    @endif
                                </td>
                                <td class="fw-bold {{ $entry->entry_type === 'charge' ? 'text-danger' : 'text-success' }}">{{ number_format($entry->amount, 0, ',', ' ') }} FCFA</td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('materiel.cost-control.financial-entries.edit', $entry) }}" class="btn btn-outline-primary"><i class="fas fa-edit"></i></a>
                                        <form method="POST" action="{{ route('materiel.cost-control.financial-entries.destroy', $entry) }}" onsubmit="return confirm('Supprimer cette écriture financière ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-4 text-muted">Aucune charge ni facture liée</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($financialEntries->hasPages())
            <div class="card-footer bg-white border-0">
                {{ $financialEntries->links() }}
            </div>
        @endif
    </div>
</div>
@endsection