@extends('layouts.app')

@section('title', 'Assurances - Liste | KENAM SERVICES')

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
    title="Gestion des Assurances"
    icon="fa-shield-alt"
    createRoute="materiel.assurances.create"
    createLabel="Ajouter Assurance"
    :exportable="true"
    searchPlaceholder="N° police, assureur, immatriculation..."
    :count="$totalVehicles"
>
    <!-- KPIs -->
    <x-slot name="kpis">
        <x-kpi-card
            title="Total Assurances"
            value="{{ $stats['total'] }}"
            icon="fa-shield-alt"
            color="primary"
        />
        <x-kpi-card
            title="Actives"
            value="{{ $stats['actives'] }}"
            icon="fa-check-circle"
            color="success"
        />
        <x-kpi-card
            title="À Renouveler"
            value="{{ $stats['a_renouveler'] }}"
            icon="fa-exclamation-circle"
            color="warning"
        />
        <x-kpi-card
            title="Expirées"
            value="{{ $stats['expirees'] }}"
            icon="fa-times-circle"
            color="danger"
        />
    </x-slot>

    <!-- Tableau d'assurances -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">Liste des Assurances</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 15%">N° Police</th>
                        <th style="width: 15%">Assureur</th>
                        <th style="width: 15%">Engin</th>
                        <th style="width: 12%">Début</th>
                        <th style="width: 12%">Fin</th>
                        <th style="width: 12%">Statut</th>
                        <th style="width: 19%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assurances as $assurance)
                    <tr>
                        <td>
                            <strong>{{ $assurance->numero_police }}</strong>
                        </td>
                        <td>{{ $assurance->assureur }}</td>
                        <td>
                            @if($assurance->vehicle)
                                <a href="{{ route('materiel.vehicules.show', $assurance->vehicle) }}" class="text-decoration-none">
                                    {{ $assurance->vehicle->immatriculation }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $assurance->date_debut->format('d/m/Y') }}</td>
                        <td>
                            <strong>{{ $assurance->date_fin->format('d/m/Y') }}</strong>
                        </td>
                        <td>
                            <span class="badge bg-{{ $assurance->alert_type }}">
                                <i class="fas fa-exclamation-circle me-1"></i>
                                {{ $assurance->alert_message }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('materiel.assurances.show', $assurance) }}" class="btn btn-outline-primary" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('materiel.assurances.edit', $assurance) }}" class="btn btn-outline-warning" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('materiel.assurances.destroy', $assurance) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette assurance ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            Aucune assurance enregistrée
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-list-layout>

<style>
.bg-danger { background-color: #dc3545 !important; }
.bg-warning { background-color: #ffc107 !important; color: #333 !important; }
.bg-success { background-color: #28a745 !important; }
.bg-primary { background-color: #007bff !important; }
</style>
@endsection
