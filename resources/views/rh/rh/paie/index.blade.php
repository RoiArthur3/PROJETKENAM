@extends('layouts.app')

@section('title', 'Gestion de la Paie - RH')

@section('content')
<x-list-layout
    title="Gestion de la Paie"
    icon="fa-money-bill-wave"
    exportRoute="rh.paie.export"
>

    <x-slot name="kpis">
        <x-kpi-card
            title="Total Brut"
            value="{{ number_format($totalBrut, 0, ',', ' ') }} FCFA"
            icon="fa-euro-sign"
            color="primary"
        />
        <x-kpi-card
            title="Total Charges"
            value="{{ number_format($totalCharges, 0, ',', ' ') }} FCFA"
            icon="fa-minus-circle"
            color="warning"
        />
        <x-kpi-card
            title="Total Net"
            value="{{ number_format($totalNet, 0, ',', ' ') }} FCFA"
            icon="fa-money-check"
            color="success"
        />
        <x-kpi-card
            title="Bulletins Payés"
            value="{{ $bulletinsPayes }}/{{ $bulletins }}"
            icon="fa-check-circle"
            color="info"
        />
    </x-slot>

    <x-slot name="filters">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Mois</label>
                <input type="month" class="form-control" id="monthFilter" value="{{ $period->format('Y-m') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Statut</label>
                <select class="form-select" id="statusFilter">
                    <option value="">Tous les statuts</option>
                    <option value="genere">Généré</option>
                    <option value="valide">Validé</option>
                    <option value="paye">Payé</option>
                    <option value="refuse">Refusé</option>
                </select>
            </div>
        </div>
    </x-slot>

    <!-- Tableau des paies -->
    <thead class="table-light">
        <tr>
            <th>Agent</th>
            <th>Période</th>
            <th>Salaire de base</th>
            <th>Total Brut</th>
            <th>CNPS</th>
            <th>Autres retenues</th>
            <th>Net à payer</th>
            <th>Statut</th>
            <th class="text-center">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($salaires as $paie)
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">{{ $paie->user->name ?? 'N/A' }}</div>
                            <small class="text-muted">{{ $paie->user->email ?? '' }}</small>
                        </div>
                    </div>
                </td>
                <td>
                    {{ $period->format('m') }}/{{ $period->format('Y') }}
                </td>
                <td>{{ number_format($paie->salaire_brut ?? 0, 0, ',', ' ') }} FCFA</td>
                <td class="fw-semibold">{{ number_format($paie->salaire_brut ?? 0, 0, ',', ' ') }} FCFA</td>
                <td>{{ number_format($paie->cnps_salariale ?? 0, 0, ',', ' ') }} FCFA</td>
                <td>{{ number_format($paie->autres_retenues ?? 0, 0, ',', ' ') }} FCFA</td>
                <td class="fw-bold text-success">{{ number_format($paie->net_a_payer ?? 0, 0, ',', ' ') }} FCFA</td>
                <td>
                    @php
                        $statusColors = [
                            'genere' => 'secondary',
                            'valide' => 'info',
                            'paye' => 'success',
                            'refuse' => 'danger',
                        ];
                        $statusLabels = [
                            'genere' => 'Généré',
                            'valide' => 'Validé',
                            'paye' => 'Payé',
                            'refuse' => 'Refusé',
                        ];
                    @endphp
                    <span class="badge bg-{{ $statusColors[$paie->statut] ?? 'secondary' }}">
                        {{ $statusLabels[$paie->statut] ?? $paie->statut }}
                    </span>
                </td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-info"
                                onclick="viewPaie({{ $paie->user->id ?? 'null' }})"
                                title="Voir détails">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-outline-warning"
                                onclick="window.location.href='{{ route('rh.paie.edit', $paie->user->id ?? 'null') }}'"
                                title="Modifier manuellement">
                            <i class="fas fa-edit"></i>
                        </button>
                        @if($paie->statut === 'genere')
                            <button type="button" class="btn btn-outline-success"
                                    onclick="validatePaie({{ $paie->user->id ?? 'null' }})"
                                    title="Valider">
                                <i class="fas fa-check"></i>
                            </button>
                        @elseif($paie->statut === 'valide')
                            <button type="button" class="btn btn-outline-primary"
                                    onclick="markAsPaid({{ $paie->user->id ?? 'null' }})"
                                    title="Marquer comme payé">
                                <i class="fas fa-money-bill-wave"></i>
                            </button>
                        @endif
                        <a href="{{ route('rh.paie.pdf', $paie->user->id ?? 'null') }}" class="btn btn-outline-secondary"
                           title="Télécharger PDF" target="_blank">
                            <i class="fas fa-download"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center py-4">
                    <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                    <div class="text-muted">Aucune fiche de paie trouvée pour ce mois</div>
                    <button type="button" class="btn btn-primary mt-2"
                            onclick="generateAllPaie()">
                        <i class="fas fa-plus"></i> Générer les paies
                    </button>
                </td>
            </tr>
        @endforelse
    </tbody>

</x-list-layout>

<script>
function filterByMonth() {
    const month = document.getElementById('monthFilter').value;
    if (month) {
        window.location.href = `?mois=${month}`;
    }
}

function filterByStatus() {
    const status = document.getElementById('statusFilter').value;
    // Implement status filtering
    console.log('Filter by status:', status);
}

function viewPaie(id) {
    // Redirect to paie show page
    window.location.href = `/rh/paie/${id}`;
}

function validatePaie(id) {
    if (confirm('Êtes-vous sûr de vouloir valider cette fiche de paie ?')) {
        // Implement validation
        console.log('Validate paie:', id);
    }
}

function markAsPaid(id) {
    if (confirm('Êtes-vous sûr de vouloir marquer cette paie comme payée ?')) {
        // Implement payment marking
        console.log('Mark as paid:', id);
    }
}

function generateAllPaie() {
    const month = document.getElementById('monthFilter').value;
    if (confirm('Générer les fiches de paie pour tous les agents du mois sélectionné ?')) {
        window.location.href = `/rh/paie/generate?mois=${month}`;
    }
}

// Event listeners
document.getElementById('monthFilter')?.addEventListener('change', filterByMonth);
document.getElementById('statusFilter')?.addEventListener('change', filterByStatus);
</script>
@endsection
