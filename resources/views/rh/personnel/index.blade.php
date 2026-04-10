@extends('layouts.app')

@section('title', 'Personnel RH - Gestion des Employés - KENAM SERVICES')

@section('content')
@php
    $colors = ['#4e73df','#1cc88a','#f6c23e','#36b9cc','#e74a3b','#858796','#5a5c69','#fd7e14'];
@endphp
<x-dashboard-layout title="Gestion du Personnel RH" icon="fa-users-cog" subtitle="Vue d'ensemble et gestion complète des employés RH">
    <!-- KPIs -->
    <x-slot name="kpis">
        <x-kpi-card
            title="Effectif Total"
            :value="$stats['total'] ?? 0"
            icon="fa-users"
            color="primary"
            subtitle="Personnel RH"
        />

        <x-kpi-card
            title="Personnels Actifs"
            :value="$stats['actifs'] ?? 0"
            icon="fa-user-check"
            color="success"
            subtitle="En poste"
        />

        <x-kpi-card
            title="En Période d'Essai"
            :value="$stats['enEssai'] ?? 0"
            icon="fa-hourglass-half"
            color="warning"
            subtitle="Nouveaux employés"
        />

        <x-kpi-card
            title="Contrats Expirants"
            :value="$stats['contratsExpirants'] ?? 0"
            icon="fa-exclamation-triangle"
            color="danger"
            subtitle="À renouveler"
        />
    </x-slot>

    <!-- Répartition par Service et Filtres -->
    <div class="row mb-4">
        <!-- Répartition par Service -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>Répartition par Service
                    </h6>
                </div>
                <div class="card-body">
                    @if(!empty($stats['parService']))
                        @foreach($stats['parService'] as $idx => $service)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <small class="fw-bold">{{ $service->service }}</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary me-2">{{ $service->nb }}</span>
                                <small class="text-muted">{{ round(($service->nb / ($stats['total'] ?? 1)) * 100, 1) }}%</small>
                            </div>
                        </div>
                        <div class="progress mb-3" style="height: 6px;">
                            <div class="progress-bar" style="width: {{ round(($service->nb / ($stats['total'] ?? 1)) * 100, 1) }}%; background-color: {{ $colors[$idx % count($colors)] }}"></div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center mb-0">Aucun service configuré</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Filtres de recherche -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-filter me-2"></i>Filtres de Recherche
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('rh.personnel.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small">Recherche</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" name="search" class="form-control"
                                       placeholder="Nom, prénom, matricule..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Service</label>
                            <select name="service" class="form-select">
                                <option value="">Tous</option>
                                @foreach($stats['parService'] as $service)
                                    <option value="{{ $service->service }}" {{ request('service') == $service->service ? 'selected' : '' }}>
                                        {{ $service->service }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Statut</label>
                            <select name="statut" class="form-select">
                                <option value="">Tous</option>
                                <option value="ACTIF" {{ request('statut') == 'ACTIF' ? 'selected' : '' }}>Actif</option>
                                <option value="CONGE" {{ request('statut') == 'CONGE' ? 'selected' : '' }}>Congé</option>
                                <option value="MALADIE" {{ request('statut') == 'MALADIE' ? 'selected' : '' }}>Maladie</option>
                                <option value="SUSPENDU" {{ request('statut') == 'SUSPENDU' ? 'selected' : '' }}>Suspendu</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Type Contrat</label>
                            <select name="type_contrat" class="form-select">
                                <option value="">Tous</option>
                                <option value="CDI" {{ request('type_contrat') == 'CDI' ? 'selected' : '' }}>CDI</option>
                                <option value="CDD" {{ request('type_contrat') == 'CDD' ? 'selected' : '' }}>CDD</option>
                                <option value="JOURNALIER" {{ request('type_contrat') == 'JOURNALIER' ? 'selected' : '' }}>Journalier</option>
                                <option value="STAGIAIRE" {{ request('type_contrat') == 'STAGIAIRE' ? 'selected' : '' }}>Stagiaire</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i>Filtrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i>Liste du Personnel RH
                                <span class="badge bg-primary ms-2">{{ $personnels instanceof \Illuminate\Pagination\LengthAwarePaginator ? $personnels->total() : $personnels->count() }}</span>
                            </h5>
                            <small class="text-muted">
                                Gestion des employés RH (séparé des comptes utilisateurs système)
                            </small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('rh.personnel.export') }}" class="btn btn-outline-success">
                                <i class="fas fa-download me-1"></i>Exporter
                            </a>
                            <a href="{{ route('rh.personnel.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Nouveau Personnel RH
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau du personnel -->
    <div class="card shadow-sm">
        <div class="card-body">
            @if($personnels->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Photo</th>
                                <th>Matricule</th>
                                <th>Nom & Prénoms</th>
                                <th>Fonction</th>
                                <th>Service</th>
                                <th>Type Contrat</th>
                                <th>Date Embauche</th>
                                <th>Ancienneté</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($personnels as $personnel)
                            <tr>
                                <td>
                                    @if(isset($personnel->photo_profil) && $personnel->photo_profil)
                                        <img src="{{ asset('storage/' . $personnel->photo_profil) }}"
                                             alt="Photo" class="rounded-circle" width="40" height="40">
                                    @else
                                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center"
                                             style="width: 40px; height: 40px;">
                                            <i class="fas fa-user text-white" style="font-size: 14px;"></i>
                                        </div>
                                    @endif
                                </td>
                                <td><strong class="text-primary">{{ $personnel->matricule ?? 'N/A' }}</strong></td>
                                <td>
                                    <div>
                                        <strong>{{ $personnel->nom ?? $personnel->name ?? 'N/A' }}</strong><br>
                                        <small class="text-muted">{{ $personnel->prenoms ?? '' }}</small>
                                    </div>
                                </td>
                                <td>{{ $personnel->poste ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $personnel->service ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $personnel->type_contrat ?? 'N/A' }}</span>
                                    @if(isset($personnel->enEssai) && $personnel->enEssai)
                                        <span class="badge bg-warning ms-1">Essai</span>
                                    @endif
                                </td>
                                <td>
                                    @if(isset($personnel->date_embauche) && $personnel->date_embauche)
                                        {{ \Carbon\Carbon::parse($personnel->date_embauche)->format('d/m/Y') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $personnel->anciennete ?? 'N/A' }} ans</span>
                                </td>
                                <td>
                                    @switch($personnel->statut ?? 'N/A')
                                        @case('ACTIF')
                                            <span class="badge bg-success">Actif</span>
                                            @break
                                        @case('CONGE')
                                            <span class="badge bg-info">Congé</span>
                                            @break
                                        @case('MALADIE')
                                            <span class="badge bg-warning">Maladie</span>
                                            @break
                                        @case('SUSPENDU')
                                            <span class="badge bg-secondary">Suspendu</span>
                                            @break
                                        @case('DEMISSION')
                                            <span class="badge bg-danger">Démission</span>
                                            @break
                                        @case('LICENCIE')
                                            <span class="badge bg-danger">Licencié</span>
                                            @break
                                        @case('RETRAITE')
                                            <span class="badge bg-dark">Retraité</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $personnel->statut ?? 'N/A' }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                                     <a href="{{ route('rh.personnel.show', $personnel->id) }}"
                                           class="btn btn-sm btn-outline-primary" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('rh.personnel.edit', $personnel->id) }}"
                                           class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                    data-bs-toggle="dropdown" title="Plus">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('rh.personnel.conges', $personnel->id) }}">
                                                        <i class="fas fa-calendar-alt me-2"></i>Congés
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('rh.personnel.paies', $personnel->id) }}">
                                                        <i class="fas fa-money-bill me-2"></i>Paies
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('rh.personnel.documents', $personnel->id) }}">
                                                        <i class="fas fa-file me-2"></i>Documents
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('rh.personnel.fiche.pdf', $personnel->id) }}">
                                                        <i class="fas fa-file-pdf me-2"></i>Fiche PDF
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('rh.personnel.destroy', $personnel->id) }}" method="POST"
                                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce personnel ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fas fa-trash me-2"></i>Supprimer
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($personnels instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <small class="text-muted">
                        Affichage de {{ $personnels->firstItem() }} à {{ $personnels->lastItem() }}
                        sur {{ $personnels->total() }} employés
                    </small>
                    {{ $personnels->links() }}
                </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users text-muted fa-3x mb-3"></i>
                    <h5 class="text-muted">Aucun personnel trouvé</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'service', 'statut', 'type_contrat']))
                            Essayez de modifier vos critères de recherche
                        @else
                            Commencez par ajouter du personnel RH
                        @endif
                    </p>
                    @if(!request()->hasAny(['search', 'service', 'statut', 'type_contrat']))
                        <a href="{{ route('rh.personnel.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Ajouter du Personnel RH
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-dashboard-layout>
@endsection

@push('scripts')
<script>
// Recherche en temps réel
let searchTimeout;
document.querySelector('input[name="search"]').addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        e.target.form.submit();
    }, 500);
});

// Confirmation de suppression
document.querySelectorAll('form[method="DELETE"]').forEach(form => {
    form.addEventListener('submit', function(e) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer ce personnel ? Cette action est irréversible.')) {
            e.preventDefault();
        }
    });
});
</script>
@endpush
