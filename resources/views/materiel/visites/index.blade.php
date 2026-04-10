@extends('layouts.app')

@section('title', 'Visites Techniques - Liste | KENAM SERVICES')

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
    title="Gestion des Visites Techniques"
    icon="fa-clipboard-check"
    createRoute="materiel.visites.create"
    createLabel="Nouvelle Visite"
    :exportable="true"
    searchPlaceholder="N° certificat, centre, immatriculation..."
>
    <!-- KPIs -->
    <x-slot name="kpis">
        <x-kpi-card
            title="Total Visites"
            value="{{ $stats['total'] }}"
            icon="fa-history"
            color="primary"
        />
        <x-kpi-card
            title="Valides"
            value="{{ $stats['valides'] }}"
            icon="fa-check-circle"
            color="success"
        />
        <x-kpi-card
            title="À Renouveler"
            value="{{ $stats['a_renouveler'] }}"
            icon="fa-clock"
            color="warning"
        />
        <x-kpi-card
            title="Expirées"
            value="{{ $stats['expirees'] }}"
            icon="fa-exclamation-triangle"
            color="danger"
        />
    </x-slot>

    <!-- Tableau des visites -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-primary">Liste des Visites Techniques</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 15%">Engin</th>
                        <th style="width: 15%">Date Visite</th>
                        <th style="width: 15%">Date Expiration</th>
                        <th style="width: 15%">Centre</th>
                        <th style="width: 10%">Coût</th>
                        <th style="width: 15%">Statut</th>
                        <th style="width: 15%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($visites as $visite)
                    @php
                        $isExpired = $visite->date_expiration->isPast();
                        $isExpiringSoon = !$isExpired && $visite->date_expiration->diffInDays(now()) <= 30;
                    @endphp
                    <tr>
                        <td>
                            @if($visite->vehicle)
                                <a href="{{ route('materiel.vehicules.show', $visite->vehicle) }}" class="fw-bold text-decoration-none">
                                    <i class="fas fa-truck me-1"></i> {{ $visite->vehicle->immatriculation }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $visite->date_visite->format('d/m/Y') }}</td>
                        <td>
                            <strong class="{{ $isExpired ? 'text-danger' : ($isExpiringSoon ? 'text-warning' : '') }}">
                                {{ $visite->date_expiration->format('d/m/Y') }}
                            </strong>
                        </td>
                        <td>{{ $visite->centre_visite ?? '-' }}</td>
                        <td>{{ number_format($visite->cout, 0, ',', ' ') }} FCFA</td>
                        <td>
                            @if($isExpired)
                                <span class="badge bg-danger rounded-pill">Expirée</span>
                            @elseif($isExpiringSoon)
                                <span class="badge bg-warning text-dark rounded-pill">À renouveler</span>
                            @else
                                <span class="badge bg-success rounded-pill">Valide</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('materiel.visites.edit', $visite) }}" class="btn btn-outline-warning" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('materiel.visites.destroy', $visite) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette visite technique ?');">
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
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="fas fa-clipboard-list fa-3x mb-3 d-block opacity-25"></i>
                            Aucune visite technique enregistrée
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($visites->hasPages())
        <div class="card-footer bg-white">
            {{ $visites->links() }}
        </div>
        @endif
    </div>
</x-list-layout>
@endsection
