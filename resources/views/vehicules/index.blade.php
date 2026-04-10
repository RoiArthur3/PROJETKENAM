@extends('layouts.app')

@section('title', 'Véhicules - Liste | KENAM SERVICES')

@section('content')
<!-- Messages de succès -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<x-list-layout
    title="Liste du Matériel Roulant"
    icon="fa-truck"
    createRoute="materiel.vehicules.create"
    createLabel="Ajouter un Matériel"
    :exportable="true"
    searchPlaceholder="Immatriculation, marque, modèle..."
    :count="$totalVehicles"
>
    <!-- KPIs -->
    <x-slot name="kpis">
        <x-kpi-card
            title="Total Matériel"
            value="{{ $totalVehicles }}"
            icon="fa-truck"
            color="primary"
        />
        <x-kpi-card
            title="Disponibles"
            value="{{ $availableCount }}"
            icon="fa-check"
            color="success"
        />
        <x-kpi-card
            title="En maintenance"
            value="1"
            icon="fa-wrench"
            color="warning"
        />
        <x-kpi-card
            title="Utilisation Moyenne"
            value="78%"
            icon="fa-tachometer-alt"
            color="info"
        />
    </x-slot>

    <!-- Filtres supplémentaires -->
    <x-slot name="filters">
        <div class="col-md-2">
            <label class="form-label">Statut</label>
            <select name="statut" class="form-select" onchange="this.form && this.form.submit()">
                <option value="" {{ request('statut') === null || request('statut') === '' ? 'selected' : '' }}>Tous</option>
                <option value="Disponible" {{ request('statut') === 'Disponible' ? 'selected' : '' }}>Disponible</option>
                <option value="En mission" {{ request('statut') === 'En mission' ? 'selected' : '' }}>En mission</option>
                <option value="En maintenance" {{ request('statut') === 'En maintenance' ? 'selected' : '' }}>En maintenance</option>
                <option value="Immobilisé" {{ request('statut') === 'Immobilisé' ? 'selected' : '' }}>Immobilisé</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Type</label>
            <select name="type" class="form-select" onchange="this.form && this.form.submit()">
                <option value="" {{ request('type') === null || request('type') === '' ? 'selected' : '' }}>Tous les types</option>
                <option value="Vehicule" {{ request('type') === 'Vehicule' ? 'selected' : '' }}>Véhicule</option>
                <option value="Machine" {{ request('type') === 'Machine' ? 'selected' : '' }}>Machine</option>
                <option value="Camion" {{ request('type') === 'Camion' ? 'selected' : '' }}>Camion</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Provenance</label>
            <select name="provenance" class="form-select" onchange="this.form && this.form.submit()">
                <option value="" {{ request('provenance') === null || request('provenance') === '' ? 'selected' : '' }}>Toutes les provenances</option>
                <option value="kenam" {{ request('provenance') === 'kenam' ? 'selected' : '' }}>Kenam Services</option>
                <option value="fournisseur" {{ request('provenance') === 'fournisseur' ? 'selected' : '' }}>Autre fournisseur</option>
            </select>
        </div>
    </x-slot>

    <!-- Tableau -->
    <thead class="table-light">
        <tr>
            <th>Immatriculation</th>
            <th>Marque</th>
            <th>Modèle</th>
            <th>Type</th>
            <th>Provenance</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($vehicules as $v)
            @php
                $statutBadge = $v->disponible ? 'success' : 'secondary';
                $statutText = $v->disponible ? 'Disponible' : 'Indisponible';
            @endphp
            <tr>
                <td><strong>{{ $v->immatriculation }}</strong></td>
                <td>{{ $v->marque }}</td>
                <td>{{ $v->modele }}</td>
                <td>
                    <span class="badge bg-secondary">{{ $v->type_materiel ?? '—' }}</span>
                </td>
                <td>
                    @if(($v->provenance ?? '') === 'kenam')
                        <span class="badge bg-primary">Kenam Services</span>
                    @elseif(($v->provenance ?? '') === 'fournisseur')
                        <span class="badge bg-info">{{ optional($v->fournisseur)->raison_sociale ?? 'Autre fournisseur' }}</span>
                    @else
                        <span class="badge bg-secondary">Non défini</span>
                    @endif
                </td>
                <td><span class="badge bg-{{ $statutBadge }}">{{ $statutText }}</span></td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('materiel.vehicules.show', $v) }}" class="btn btn-outline-primary" title="Voir">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('materiel.vehicules.edit', $v) }}" class="btn btn-outline-warning" title="Modifier">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('materiel.vehicules.destroy', $v) }}" method="POST" onsubmit="return confirm('Supprimer définitivement ce véhicule ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center text-muted py-4">Aucun véhicule enregistré</td>
            </tr>
        @endforelse
    </tbody>
    @if(method_exists($vehicules, 'hasPages') && $vehicules->hasPages())
        <tfoot>
            <tr>
                <td colspan="7">{{ $vehicules->links() }}</td>
            </tr>
        </tfoot>
    @endif

    <!-- Pagination -->
    <x-slot name="pagination">
        {{ $vehicules->links() }}
    </x-slot>
</x-list-layout>

<script>
function voirHistorique(id) {
    alert('Historique du véhicule ' + id + ' - Fonctionnalité en développement');
}
</script>
@endsection
