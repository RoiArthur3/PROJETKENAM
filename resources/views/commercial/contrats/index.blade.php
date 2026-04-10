@extends('layouts.app')

@section('title', 'Contrats Commerciaux - Commercial')

@section('content')
<x-list-layout
    title="Gestion des Contrats Commerciaux"
    icon="fa-file-contract"
    createRoute="commercial.contrats.create"
    createText="Nouveau Contrat"
    exportRoute="commercial.contrats.export"
>

    <x-slot name="kpis">
        <x-kpi-card
            title="Total Contrats"
            value="{{ $contrats->count() ?? 0 }}"
            icon="fa-file-contract"
            color="primary"
        />
        <x-kpi-card
            title="Contrats Actifs"
            value="{{ $contrats->where('statut', 'actif')->count() ?? 0 }}"
            icon="fa-check-circle"
            color="success"
        />
        <x-kpi-card
            title="Contrats Expirés"
            value="{{ $contrats->where('statut', 'expire')->count() ?? 0 }}"
            icon="fa-exclamation-triangle"
            color="warning"
        />
        <x-kpi-card
            title="Valeur Totale"
            value="{{ number_format($contrats->sum('montant') ?? 0, 0, ',', ' ') }} FCFA"
            icon="fa-euro-sign"
            color="info"
        />
    </x-slot>

    <x-slot name="filters">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Client</label>
                <input type="text" class="form-control" id="clientFilter" placeholder="Nom du client">
            </div>
            <div class="col-md-3">
                <label class="form-label">Type de contrat</label>
                <select class="form-select" id="typeFilter">
                    <option value="">Tous les types</option>
                    <option value="service">Service</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="consulting">Consulting</option>
                    <option value="formation">Formation</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Statut</label>
                <select class="form-select" id="statusFilter">
                    <option value="">Tous les statuts</option>
                    <option value="actif">Actif</option>
                    <option value="expire">Expiré</option>
                    <option value="suspendu">Suspendu</option>
                    <option value="annule">Annulé</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Date d'expiration</label>
                <select class="form-select" id="expiryFilter">
                    <option value="">Toutes les dates</option>
                    <option value="this_month">Ce mois</option>
                    <option value="next_month">Mois prochain</option>
                    <option value="this_quarter">Ce trimestre</option>
                    <option value="expired">Déjà expirés</option>
                </select>
            </div>
        </div>
    </x-slot>

    <!-- Tableau des contrats -->
    <thead class="table-light">
        <tr>
            <th>Client</th>
            <th>Type</th>
            <th>Montant</th>
            <th>Date de début</th>
            <th>Date d'expiration</th>
            <th>Statut</th>
            <th class="text-center">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($contrats ?? collect() as $contrat)
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                            <i class="fas fa-building"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">{{ $contrat->client_nom ?? 'Client Test' }}</div>
                            <small class="text-muted">{{ $contrat->client_contact ?? 'contact@client.com' }}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge bg-info">{{ $contrat->type_contrat ?? 'Service' }}</span>
                </td>
                <td class="fw-semibold">{{ number_format($contrat->montant ?? 0, 0, ',', ' ') }} FCFA</td>
                <td>{{ $contrat->date_debut ? \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') : 'N/A' }}</td>
                <td>
                    @php
                        $expiryDate = $contrat->date_fin ? \Carbon\Carbon::parse($contrat->date_fin) : null;
                        $isExpiringSoon = $expiryDate && $expiryDate->isBetween(now(), now()->addDays(30));
                        $isExpired = $expiryDate && $expiryDate->isPast();
                    @endphp
                    <span class="{{ $isExpired ? 'text-danger fw-bold' : ($isExpiringSoon ? 'text-warning fw-bold' : '') }}">
                        {{ $expiryDate ? $expiryDate->format('d/m/Y') : 'N/A' }}
                    </span>
                    @if($isExpiringSoon)
                        <br><small class="text-warning"><i class="fas fa-exclamation-triangle"></i> Expire bientôt</small>
                    @elseif($isExpired)
                        <br><small class="text-danger"><i class="fas fa-times-circle"></i> Expiré</small>
                    @endif
                </td>
                <td>
                    @php
                        $statusColors = [
                            'actif' => 'success',
                            'expire' => 'warning',
                            'suspendu' => 'secondary',
                            'annule' => 'danger',
                        ];
                        $statusLabels = [
                            'actif' => 'Actif',
                            'expire' => 'Expiré',
                            'suspendu' => 'Suspendu',
                            'annule' => 'Annulé',
                        ];
                    @endphp
                    <span class="badge bg-{{ $statusColors[$contrat->statut ?? 'actif'] ?? 'secondary' }}">
                        {{ $statusLabels[$contrat->statut ?? 'actif'] ?? 'Inconnu' }}
                    </span>
                </td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-info"
                                onclick="viewContract({{ $contrat->id ?? 1 }})"
                                title="Voir détails">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-outline-warning"
                                onclick="editContract({{ $contrat->id ?? 1 }})"
                                title="Modifier">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-outline-success"
                                onclick="renewContract({{ $contrat->id ?? 1 }})"
                                title="Renouveler">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary"
                                onclick="downloadContract({{ $contrat->id ?? 1 }})"
                                title="Télécharger">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center py-4">
                    <i class="fas fa-file-contract fa-3x text-muted mb-3"></i>
                    <div class="text-muted">Aucun contrat commercial trouvé</div>
                    <button type="button" class="btn btn-primary mt-2"
                            onclick="window.location.href='{{ route('commercial.contrats.create') }}'">
                        <i class="fas fa-plus"></i> Créer le premier contrat
                    </button>
                </td>
            </tr>
        @endforelse
    </tbody>

</x-list-layout>

<script>
function viewContract(id) {
    window.location.href = `/commercial/contrats/${id}`;
}

function editContract(id) {
    window.location.href = `/commercial/contrats/${id}/edit`;
}

function renewContract(id) {
    if (confirm('Voulez-vous renouveler ce contrat ?')) {
        // Implement renewal logic
        console.log('Renew contract:', id);
    }
}

function downloadContract(id) {
    window.open(`/commercial/contrats/${id}/download`, '_blank');
}

// Filtres
document.getElementById('clientFilter')?.addEventListener('input', applyFilters);
document.getElementById('typeFilter')?.addEventListener('change', applyFilters);
document.getElementById('statusFilter')?.addEventListener('change', applyFilters);
document.getElementById('expiryFilter')?.addEventListener('change', applyFilters);

function applyFilters() {
    const client = document.getElementById('clientFilter').value.toLowerCase();
    const type = document.getElementById('typeFilter').value;
    const status = document.getElementById('statusFilter').value;
    const expiry = document.getElementById('expiryFilter').value;

    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        if (row.querySelector('td[colspan]')) return; // Skip empty state row

        const rowClient = row.cells[0].textContent.toLowerCase();
        const rowType = row.cells[1].textContent.toLowerCase();
        const rowStatus = row.cells[5].textContent.toLowerCase();

        let show = true;

        if (client && !rowClient.includes(client)) show = false;
        if (type && !rowType.includes(type)) show = false;
        if (status && !rowStatus.includes(status)) show = false;

        // Expiry filter would need date parsing - simplified for now
        if (expiry) {
            // Add expiry date filtering logic here if needed
        }

        row.style.display = show ? '' : 'none';
    });
}
</script>
@endsection
