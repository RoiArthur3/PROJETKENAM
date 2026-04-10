@extends('layouts.app')

@section('title', 'Encaissements - KENAM SERVICES')

@section('content')
<div class="container-fluid py-4">
    {{-- Messages Flash --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Succès !</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Erreur !</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">
                <i class="fas fa-arrow-circle-down text-success me-2"></i>Encaissements
            </h1>
            <p class="text-muted mb-0">Gestion et suivi des entrées de fonds en trésorerie</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.dashboard') }}" class="btn btn-light shadow-sm">
                <i class="fas fa-chart-pie me-2"></i>Dashboard
            </a>
            <a href="{{ route('tresorerie.encaissements.create') }}" class="btn btn-primary shadow-sm px-4">
                <i class="fas fa-plus-circle me-2"></i>Nouvel Encaissement
            </a>
        </div>
    </div>

    {{-- KPIs Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm overflow-hidden h-100">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-success text-uppercase opacity-75 mb-1">Total Montant</div>
                            <div class="h4 mb-0 fw-bold text-dark">{{ number_format($encaissements->sum('montant'), 0, ',', ' ') }} <small class="text-muted">FCFA</small></div>
                        </div>
                        <div class="bg-success-subtle p-3 rounded-circle">
                            <i class="fas fa-coins text-success fa-lg"></i>
                        </div>
                    </div>
                    <div class="mt-3 small">
                        <span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i>{{ $encaissements->count() }}</span>
                        <span class="text-muted">opérations validées</span>
                    </div>
                </div>
                <div class="bg-success position-absolute bottom-0 start-0 w-100" style="height: 4px; opacity: 0.1"></div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm overflow-hidden h-100">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-primary text-uppercase opacity-75 mb-1">Factures Encaissées</div>
                            <div class="h4 mb-0 fw-bold text-dark">{{ $encaissements->whereNotNull('invoice_id')->count() }}</div>
                        </div>
                        <div class="bg-primary-subtle p-3 rounded-circle">
                            <i class="fas fa-file-invoice-dollar text-primary fa-lg"></i>
                        </div>
                    </div>
                    <div class="mt-3 small">
                        <span class="text-muted">Total: </span>
                        <span class="fw-bold">{{ number_format($encaissements->whereNotNull('invoice_id')->sum('montant'), 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>
                <div class="bg-primary position-absolute bottom-0 start-0 w-100" style="height: 4px; opacity: 0.1"></div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm overflow-hidden h-100">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-info text-uppercase opacity-75 mb-1">Virements & Chèques</div>
                            <div class="h4 mb-0 fw-bold text-dark">{{ $encaissements->whereIn('mode_paiement', ['Virement', 'Chèque', 'virement', 'cheque'])->count() }}</div>
                        </div>
                        <div class="bg-info-subtle p-3 rounded-circle">
                            <i class="fas fa-university text-info fa-lg"></i>
                        </div>
                    </div>
                    <div class="mt-3 small text-muted">Modes sécurisés</div>
                </div>
                <div class="bg-info position-absolute bottom-0 start-0 w-100" style="height: 4px; opacity: 0.1"></div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm overflow-hidden h-100">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-warning text-uppercase opacity-75 mb-1">Encaissements / Projets</div>
                            <div class="h4 mb-0 fw-bold text-dark">{{ $encaissements->whereNotNull('project_id')->count() }}</div>
                        </div>
                        <div class="bg-warning-subtle p-3 rounded-circle">
                            <i class="fas fa-project-diagram text-warning fa-lg"></i>
                        </div>
                    </div>
                    <div class="mt-3 small text-muted">Affectations directes</div>
                </div>
                <div class="bg-warning position-absolute bottom-0 start-0 w-100" style="height: 4px; opacity: 0.1"></div>
            </div>
        </div>
    </div>

    {{-- Filters & Search --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-md-3">
                    <div class="input-group input-group-merge border rounded-pill px-3 py-1">
                        <span class="input-group-text bg-transparent border-0 p-0 me-2"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" class="form-control border-0 p-0 shadow-none bg-transparent" placeholder="Référence, titre, client..." id="searchInput">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select border-0 shadow-none bg-light rounded-pill px-3 h-100" id="modeFilter">
                        <option value="">Tous les modes</option>
                        <option value="Espèces">Espèces</option>
                        <option value="Virement">Virement</option>
                        <option value="Chèque">Chèque</option>
                        <option value="Mobile Money">Mobile Money</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select border-0 shadow-none bg-light rounded-pill px-3 h-100" id="clientFilter">
                        <option value="">Tous les clients</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->nom }}">{{ $client->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-secondary w-100 rounded-pill h-100" onclick="resetFilters()">
                        <i class="fas fa-sync-alt me-2"></i>Réinitialiser
                    </button>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-secondary w-100 rounded-pill h-100" onclick="exportEncaissements()">
                        <i class="fas fa-download me-2"></i>Exporter
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-muted small text-uppercase">
                            <th class="ps-4 py-3">Référence / Date</th>
                            <th class="py-3">Type & Destination</th>
                            <th class="py-3">Client / Versant</th>
                            <th class="py-3">Caisse / Mode</th>
                            <th class="py-3 text-end">Montant</th>
                            <th class="py-3 text-center pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="encaissementsTableBody">
                        @forelse($encaissements as $encaissement)
                        <tr class="encaissement-row">
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $encaissement->reference }}</div>
                                <div class="text-muted small">
                                    <i class="far fa-calendar-alt me-1"></i>{{ \Carbon\Carbon::parse($encaissement->date_encaissement)->format('d/m/Y') }}
                                    <span class="ms-1 opacity-50">{{ \Carbon\Carbon::parse($encaissement->date_encaissement)->format('H:i') }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="badge {{ $encaissement->invoice_id ? 'bg-primary-subtle text-primary border-primary-subtle' : 'bg-secondary-subtle text-secondary' }} mb-1 border px-2 py-1">
                                    {{ strtoupper($encaissement->type_encaissement) }}
                                </div>
                                @if($encaissement->invoice)
                                    <div class="small fw-semibold text-dark">Facture: <span class="text-primary">{{ $encaissement->invoice->invoice_number }}</span></div>
                                @elseif($encaissement->operation)
                                    <div class="small text-muted">Op: {{ Str::limit($encaissement->operation->titre, 25) }}</div>
                                @elseif($encaissement->project)
                                    <div class="small fw-semibold text-warning">Projet: {{ Str::limit($encaissement->project->nom, 25) }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $encaissement->client ?? ($encaissement->clientRel->nom ?? '—') }}</div>
                                @if($encaissement->client_id)
                                    <span class="badge bg-light text-muted border-0 p-0 small">Client Identifié</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="badge bg-info-subtle text-info border border-info-subtle mb-1 align-self-start">{{ $encaissement->caisse->nom ?? 'N/A' }}</span>
                                    <small class="text-muted"><i class="fas fa-wallet fa-xs me-1"></i>{{ $encaissement->mode_paiement }}</small>
                                </div>
                            </td>
                            <td class="text-end fw-bold text-dark pe-3" style="font-size: 1.05rem;">
                                {{ number_format($encaissement->montant, 0, ',', ' ') }}
                                <span class="small text-muted fw-normal">FCFA</span>
                            </td>
                            <td class="text-center pe-4">
                                <div class="btn-group shadow-sm">
                                    <a class="btn btn-sm btn-white" href="{{ route('tresorerie.encaissements.show', $encaissement->id) }}" title="Voir détails">
                                        <i class="fas fa-eye text-primary"></i>
                                    </a>
                                    <a class="btn btn-sm btn-white" href="{{ route('tresorerie.encaissements.show', $encaissement->id) }}?print=1" target="_blank" title="Imprimer reçu">
                                        <i class="fas fa-print text-success"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="py-4">
                                    <i class="fas fa-inbox fa-3x text-light mb-3"></i>
                                    <h5 class="text-muted">Aucun encaissement trouvé</h5>
                                    <p class="text-muted small">Commencez par enregistrer une entrée de fonds.</p>
                                    <a href="{{ route('tresorerie.encaissements.create') }}" class="btn btn-primary btn-sm mt-3">
                                        <i class="fas fa-plus me-1"></i>Nouveau
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .bg-success-subtle { background-color: rgba(16, 185, 129, 0.1) !important; }
    .bg-primary-subtle { background-color: rgba(59, 130, 246, 0.1) !important; }
    .bg-info-subtle { background-color: rgba(6, 182, 212, 0.1) !important; }
    .bg-warning-subtle { background-color: rgba(245, 158, 11, 0.1) !important; }
    
    .btn-white { background: #fff; border: 1px solid #e2e8f0; }
    .btn-white:hover { background: #f8fafc; border-color: #cbd5e1; }
    
    .input-group-merge { background: transparent; }
    .input-group-merge:focus-within { border-color: #3b82f6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
    
    .encaissement-row { transition: background 0.15s ease; }
    .encaissement-row:hover { background-color: #f1f5f9 !important; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const modeFilter = document.getElementById('modeFilter');
    const clientFilter = document.getElementById('clientFilter');
    const rows = document.querySelectorAll('.encaissement-row');

    function performFilter() {
        const query = searchInput.value.toLowerCase();
        const mode = modeFilter.value.toLowerCase();
        const client = clientFilter.value.toLowerCase();

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const modeText = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
            const clientText = row.querySelector('td:nth-child(3)').textContent.toLowerCase();

            const matchesSearch = !query || text.includes(query);
            const matchesMode = !mode || modeText.includes(mode);
            const matchesClient = !client || clientText.includes(client);

            if (matchesSearch && matchesMode && matchesClient) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', performFilter);
    modeFilter.addEventListener('change', performFilter);
    clientFilter.addEventListener('change', performFilter);
});

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('modeFilter').value = '';
    document.getElementById('clientFilter').value = '';
    document.querySelectorAll('.encaissement-row').forEach(row => row.style.display = '');
}

function exportEncaissements() {
    window.location.href = "{{ route('reporting.financier') }}?export=encaissements";
}
</script>
@endpush
@endsection
