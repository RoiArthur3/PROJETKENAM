@extends('layouts.app')

@section('title', 'Détails du Projet de Location | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('materiel.missions.index') }}" class="btn btn-light border"><i class="fas fa-arrow-left me-1"></i>Retour à la liste</a>
        <div class="btn-group">
            <a href="{{ route('materiel.cost-control.create', ['mission' => $mission->id]) }}" class="btn btn-primary"><i class="fas fa-stopwatch me-1"></i>Pointer l'engin</a>
            <a href="{{ route('materiel.cost-control.financial-entries.create', ['mission' => $mission->id]) }}" class="btn btn-outline-primary"><i class="fas fa-wallet me-1"></i>Ajouter charge / CA</a>
            <a href="{{ route('materiel.missions.edit', $mission) }}" class="btn btn-warning"><i class="fas fa-edit me-1"></i>Modifier</a>
            <button class="btn btn-outline-dark" onclick="window.print()"><i class="fas fa-print me-1"></i>Imprimer</button>
        </div>
    </div>

    <div class="row">
        <!-- Infos Principales -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-primary">Projet de location {{ $mission->reference }}</h5>
                        @php
                            $statusClass = [
                                'planned' => 'bg-info',
                                'ongoing' => 'bg-primary',
                                'done' => 'bg-success',
                                'canceled' => 'bg-secondary'
                            ][$mission->status] ?? 'bg-dark';

                            $statusLabel = [
                                'planned' => 'En attente',
                                'ongoing' => 'En cours',
                                'done' => 'Terminée',
                                'canceled' => 'Annulée'
                            ][$mission->status] ?? ucfirst($mission->status);
                        @endphp
                        <span class="badge {{ $statusClass }} fs-6">{{ $statusLabel }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold">Véhicule / Engin</label>
                            <div class="d-flex align-items-center mt-1">
                                <div class="avatar-md bg-primary-subtle text-primary rounded p-3 me-3">
                                    <i class="fas fa-truck-monster fa-2x"></i>
                                </div>
                                <div>
                                    <div class="h5 mb-0 fw-bold">{{ $mission->vehicle->immatriculation }}</div>
                                    <div class="text-muted">{{ $mission->vehicle->marque }} {{ $mission->vehicle->modele }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold">Conducteur</label>
                            <div class="d-flex align-items-center mt-1">
                                <div class="avatar-md bg-success-subtle text-success rounded p-3 me-3">
                                    <i class="fas fa-user-circle fa-2x"></i>
                                </div>
                                <div>
                                    <div class="h5 mb-0 fw-bold">{{ $mission->driver->name }}</div>
                                    <div class="text-muted">{{ $mission->driver->email }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="text-muted small text-uppercase fw-bold">Destination</label>
                            <div class="fw-bold fs-5"><i class="fas fa-map-marker-alt text-danger me-2"></i>{{ $mission->destination }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small text-uppercase fw-bold">Période</label>
                            <div class="fw-bold">
                                Du {{ $mission->start_at->format('d/m/Y') }}<br>
                                Au {{ $mission->end_at->format('d/m/Y') }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small text-uppercase fw-bold">Durée / KM</label>
                            <div class="fw-bold fs-5">{{ $mission->duration_days }} jours</div>
                            <div class="text-muted small">Départ : {{ $mission->start_km ?? 'N/A' }} KM</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small text-uppercase fw-bold">Profil pointage / facturation</label>
                            @php
                                $profile = $mission->resolvePointageProfile();
                            @endphp
                            <div class="fw-bold">{{ $profile['submodule'] === 'camion_plateau' ? 'Camion Plateau' : 'Standard' }}</div>
                            <div class="text-muted small">
                                @if($profile['billing_mode'] === 'monthly')
                                    Facturation au mois
                                @elseif($profile['billing_mode'] === 'trip')
                                    Facturation au voyage
                                @else
                                    Facturation standard
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($mission->notes)
                    <div class="mt-4 p-3 bg-light rounded">
                        <label class="text-muted small text-uppercase fw-bold">Notes & Observations</label>
                        <p class="mb-0">{{ $mission->notes }}</p>
                    </div>
                    @endif

                    @if($mission->source_reference || $mission->source_type)
                    <div class="mt-4 p-3 border rounded bg-light">
                        <label class="text-muted small text-uppercase fw-bold">Document Source</label>
                        <div class="fw-bold">{{ $mission->source_reference ?? 'Sans référence' }}</div>
                        <div class="small text-muted">{{ $mission->source_type ? str_replace('_', ' ', ucfirst($mission->source_type)) : 'Rattachement manuel' }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Infos Financières -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4 bg-primary text-white">
                <div class="card-body">
                    <h6 class="text-uppercase fw-bold opacity-75">Marge Brute Réelle</h6>
                    <div class="display-5 fw-bold">{{ number_format($mission->actual_gross_margin, 0, ',', ' ') }} FCFA</div>
                    <div class="mt-3 opacity-75">
                        <i class="fas fa-chart-line me-1"></i> Rentabilité :
                        {{ $mission->actual_client_revenue > 0 ? round(($mission->actual_gross_margin / $mission->actual_client_revenue) * 100, 1) : 0 }}%
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">Pointage Réel</h6>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Heures pointées :</span>
                        <span class="fw-bold">{{ number_format($mission->actual_work_hours, 2, ',', ' ') }} h</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Jours pointés :</span>
                        <span class="fw-bold">{{ number_format($mission->actual_work_days, 2, ',', ' ') }} j</span>
                    </div>

                    <div class="small text-muted">
                        {{ $mission->pointages->count() }} pointage(s) enregistré(s)
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">Détails Financiers</h6>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Prix Jour Client :</span>
                        <span class="fw-bold text-success">{{ number_format($mission->daily_client_price, 0, ',', ' ') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">CA retenu :</span>
                        <span class="fw-bold text-success">{{ number_format($mission->actual_client_revenue, 0, ',', ' ') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                        <span class="text-muted">CA facturé :</span>
                        <span class="fw-bold text-success">{{ number_format($mission->recognized_revenue, 0, ',', ' ') }}</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Coût pointage :</span>
                        <span class="fw-bold text-danger">{{ number_format($mission->pointage_supplier_cost, 0, ',', ' ') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Charges liées :</span>
                        <span class="fw-bold text-danger">{{ number_format($mission->actual_additional_charges, 0, ',', ' ') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                        <span class="text-muted">Total Coût :</span>
                        <span class="fw-bold text-danger">{{ number_format($mission->actual_supplier_cost, 0, ',', ' ') }}</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Prix Jour Fournisseur :</span>
                        <span class="fw-bold text-danger">{{ number_format($mission->daily_supplier_price, 0, ',', ' ') }}</span>
                    </div>

                    <div class="mt-3">
                        <label class="text-muted small text-uppercase fw-bold d-block mb-2">Parties Prenantes</label>
                        <div class="p-2 border rounded mb-2">
                            <div class="small text-muted">Client :</div>
                            <div class="fw-bold">{{ $mission->client->raison_sociale ?? 'Non spécifié' }}</div>
                        </div>
                        <div class="p-2 border rounded">
                            <div class="small text-muted">Fournisseur :</div>
                            <div class="fw-bold">{{ $mission->supplier->raison_sociale ?? 'Gestion Interne' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mt-2">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-primary">Charges liées et chiffre d'affaires</h6>
            <span class="small text-muted">Trésorerie et Facture</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Libellé</th>
                            <th>Nature</th>
                            <th>Source</th>
                            <th>Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mission->financialEntries->sortByDesc('transaction_date') as $entry)
                            <tr>
                                <td>{{ $entry->transaction_date?->format('d/m/Y') }}</td>
                                <td>
                                    <div class="fw-bold">{{ $entry->label }}</div>
                                    @if($entry->notes)
                                        <div class="small text-muted">{{ $entry->notes }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold {{ $entry->type === 'expense' ? 'text-danger' : 'text-success' }}">{{ $entry->type === 'expense' ? 'Charge' : 'CA' }}</div>
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
                                <td class="fw-bold {{ $entry->type === 'expense' ? 'text-danger' : 'text-success' }}">{{ number_format($entry->amount, 0, ',', ' ') }} FCFA</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Aucune charge liée, aucune avance chantier ni facture client reliée à cette mission.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .btn, .header, .sidebar-desktop { display: none !important; }
        .main-content { margin-left: 0 !important; padding-top: 0 !important; }
        .card { box-shadow: none !important; border: 1px solid #ddd !important; }
    }
    .avatar-md {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
    }
    .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1); }
    .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1); }
</style>
@endsection
