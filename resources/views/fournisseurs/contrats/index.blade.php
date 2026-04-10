@extends('layouts.app')

@section('title', 'Contrats Fournisseurs - Fournisseurs')

@section('content')
<x-list-layout
    title="Contrats Fournisseurs"
    icon="fa-file-contract"
    createRoute="fournisseurs.contrats.create"
    createText="Nouveau Contrat"
    exportRoute="fournisseurs.contrats.export"
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
            title="Avances Totales"
            value="{{ number_format($contrats->sum('avances_versees') ?? 0, 0, ',', ' ') }} FCFA"
            icon="fa-money-bill-wave"
            color="info"
        />
        <x-kpi-card
            title="Contrats Expirés"
            value="{{ $contrats->where('statut', 'expire')->count() ?? 0 }}"
            icon="fa-exclamation-triangle"
            color="warning"
        />
    </x-slot>

    <x-slot name="filters">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Fournisseur</label>
                <input type="text" class="form-control" id="fournisseurFilter" placeholder="Nom du fournisseur">
            </div>
            <div class="col-md-4">
                <label class="form-label">Statut</label>
                <select class="form-select" id="statusFilter">
                    <option value="">Tous les statuts</option>
                    <option value="actif">Actif</option>
                    <option value="expire">Expiré</option>
                    <option value="suspendu">Suspendu</option>
                    <option value="annule">Annulé</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Date d'expiration</label>
                <select class="form-select" id="expiryFilter">
                    <option value="">Toutes les dates</option>
                    <option value="this_month">Ce mois</option>
                    <option value="next_month">Mois prochain</option>
                    <option value="this_quarter">Ce trimestre</option>
                </select>
            </div>
        </div>
    </x-slot>

    <!-- Tableau des contrats -->
    <thead class="table-light">
        <tr>
            <th>Fournisseur</th>
            <th>Objet du Contrat</th>
            <th>Montant Total</th>
            <th>Date de début</th>
            <th>Date de fin</th>
            <th>Avances Versées</th>
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
                            <div class="fw-semibold">{{ $contrat->fournisseur_nom ?? 'Fournisseur Test' }}</div>
                            <small class="text-muted">{{ $contrat->fournisseur_contact ?? 'contact@fournisseur.com' }}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <div>
                        <div class="fw-semibold">{{ $contrat->objet ?? 'Fourniture de matériel' }}</div>
                        <small class="text-muted">{{ $contrat->numero_contrat ?? 'N° ' . $contrat->id }}</small>
                    </div>
                </td>
                <td class="fw-semibold">{{ number_format($contrat->montant_total ?? 0, 0, ',', ' ') }} FCFA</td>
                <td>{{ $contrat->date_debut ? \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') : 'N/A' }}</td>
                <td>
                    @php
                        $dateFin = $contrat->date_fin ? \Carbon\Carbon::parse($contrat->date_fin) : null;
                        $isExpired = $dateFin && $dateFin->isPast();
                    @endphp
                    <span class="{{ $isExpired ? 'text-danger fw-bold' : '' }}">
                        {{ $dateFin ? $dateFin->format('d/m/Y') : 'N/A' }}
                    </span>
                    @if($isExpired)
                        <br><small class="text-danger"><i class="fas fa-times-circle"></i> Expiré</small>
                    @endif
                </td>
                <td>
                    <div>
                        <div class="fw-semibold text-success">{{ number_format($contrat->avances_versees ?? 0, 0, ',', ' ') }} FCFA</div>
                        @if(($contrat->avances_versees ?? 0) > 0)
                            <small class="text-muted">
                                ({{ number_format((($contrat->avances_versees ?? 0) / ($contrat->montant_total ?? 1)) * 100, 1) }}% du total)
                            </small>
                        @endif
                    </div>
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
                                onclick="addAdvance({{ $contrat->id ?? 1 }})"
                                title="Ajouter avance">
                            <i class="fas fa-plus-circle"></i>
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
                <td colspan="8" class="text-center py-4">
                    <i class="fas fa-file-contract fa-3x text-muted mb-3"></i>
                    <div class="text-muted">Aucun contrat fournisseur trouvé</div>
                    <button type="button" class="btn btn-primary mt-2"
                            onclick="window.location.href='{{ route('fournisseurs.contrats.create') }}'">
                        <i class="fas fa-plus"></i> Créer le premier contrat
                    </button>
                </td>
            </tr>
        @endforelse
    </tbody>

</x-list-layout>

<script>
function viewContract(id) {
    window.location.href = `/fournisseurs/contrats/${id}`;
}

function editContract(id) {
    window.location.href = `/fournisseurs/contrats/${id}/edit`;
}

function addAdvance(id) {
    // Implement add advance functionality
    console.log('Add advance for contract:', id);
}

function downloadContract(id) {
    window.open(`/fournisseurs/contrats/${id}/download`, '_blank');
}

// Filtres
document.getElementById('fournisseurFilter')?.addEventListener('input', applyFilters);
document.getElementById('statusFilter')?.addEventListener('change', applyFilters);
document.getElementById('expiryFilter')?.addEventListener('change', applyFilters);

function applyFilters() {
    const fournisseur = document.getElementById('fournisseurFilter').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;
    const expiry = document.getElementById('expiryFilter').value;

    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        if (row.querySelector('td[colspan]')) return; // Skip empty state row

        const rowFournisseur = row.cells[0].textContent.toLowerCase();
        const rowStatus = row.cells[6].textContent.toLowerCase();

        let show = true;

        if (fournisseur && !rowFournisseur.includes(fournisseur)) show = false;
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
