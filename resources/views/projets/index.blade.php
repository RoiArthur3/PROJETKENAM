@extends('layouts.app')

@section('title', 'Liste des Projets - KENAM SERVICES')

@section('content')
<x-list-layout
    title="Liste des Projets"
    icon="fa-list"
    createRoute="projets.create"
    createText="Nouveau Projet"
>

    <x-slot name="filters">
        <form method="GET" action="{{ route('projets.index') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="">Tous</option>
                        <option value="en_cours" {{ request('statut')=='en_cours' ? 'selected' : '' }}>En cours</option>
                        <option value="terminee" {{ request('statut')=='terminee' ? 'selected' : '' }}>Terminé</option>
                        <option value="en_retard" {{ request('statut')=='en_retard' ? 'selected' : '' }}>En retard</option>
                        <option value="en_attente" {{ request('statut')=='en_attente' ? 'selected' : '' }}>En attente</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Client</label>
                    <select name="client_id" class="form-select">
                        <option value="">Tous</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ (string)request('client_id') === (string)$client->id ? 'selected' : '' }}>
                                {{ $client->nom ?? $client->name ?? ('Client #'.$client->id) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Priorité</label>
                    <select name="priorite" class="form-select">
                        <option value="">Toutes</option>
                        <option value="haute" {{ request('priorite')=='haute' ? 'selected' : '' }}>Haute</option>
                        <option value="moyenne" {{ request('priorite')=='moyenne' ? 'selected' : '' }}>Moyenne</option>
                        <option value="basse" {{ request('priorite')=='basse' ? 'selected' : '' }}>Basse</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Du</label>
                    <input type="date" name="date_debut" value="{{ request('date_debut') }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Au</label>
                    <input type="date" name="date_fin" value="{{ request('date_fin') }}" class="form-control">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-3 ms-auto">
                    <button class="btn btn-primary w-100" type="submit">
                        <i class="fas fa-search me-2"></i>Filtrer
                    </button>
                </div>
            </div>
        </form>
    </x-slot>

    <!-- Statistiques Rapides -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">En Cours</div>
                            <div class="h4 mb-0">{{ $stats['en_cours'] ?? 0 }}</div>
                        </div>
                        <i class="fas fa-spinner fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Terminés</div>
                            <div class="h4 mb-0">{{ $stats['terminees'] ?? 0 }}</div>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">En Retard</div>
                            <div class="h4 mb-0">{{ $stats['en_retard'] ?? 0 }}</div>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Total Projets</div>
                            <div class="h4 mb-0">{{ $stats['total'] ?? 0 }}</div>
                        </div>
                        <i class="fas fa-coins fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des Projets -->
    <div class="card shadow">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-table me-2"></i>Projets Actifs et Récents
            </h6>
            <div class="d-flex gap-2">
                <a href="#" class="btn btn-sm btn-outline-secondary"><i class="fas fa-file-export me-1"></i>Exporter</a>
                <a href="{{ route('projets.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i>Nouveau Projet</a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="projetsTable">
                    <thead class="table-light">
                        <tr>
                            <th>N° Projet</th>
                            <th>Client</th>
                            <th>Contrat</th>
                            <th>Trajet</th>
                            <th>Date Début</th>
                            <th>Date Fin</th>
                            <th>Budget (FCFA)</th>
                            <th>Avancement</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projets as $projet)
                            <tr>
                                <td>
                                    <a href="{{ route('projets.show', $projet) }}" class="fw-bold text-decoration-none">
                                        {{ $projet->numero_projet }}
                                    </a>
                                </td>
                                <td>{{ optional($projet->client)->nom ?? optional($projet->client)->name ?? 'N/A' }}</td>
                                <td>-</td>
                                <td>{{ $projet->service ?? '-' }}</td>
                                <td>{{ optional($projet->created_at)->format('d/m/Y') }}</td>
                                <td>{{ optional($projet->echeance)->format('d/m/Y') }}</td>
                                <td class="fw-bold">-</td>
                                <td>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-primary" style="width: 50%"></div>
                                    </div>
                                    <small class="text-muted">N/A</small>
                                </td>
                                <td>
                                    @php $statut = $projet->statut_courant; @endphp
                                    <span class="badge
                                        @if($statut === 'terminee') bg-success
                                        @elseif($statut === 'en_retard') bg-danger
                                        @elseif($statut === 'en_cours') bg-primary
                                        @else bg-secondary @endif">
                                        {{ $statut ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('projets.avancement', $projet) }}" class="btn btn-sm btn-primary" title="Suivi"><i class="fas fa-tasks"></i></a>
                                    <a href="{{ route('projets.affectations', $projet) }}" class="btn btn-sm btn-success" title="Ressources"><i class="fas fa-user-cog"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted">Aucun projet trouvé pour ces critères.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(method_exists($projets, 'links'))
                <div class="mt-3 d-flex justify-content-end">
                    {{ $projets->links() }}
                </div>
            @endif
        </div>
    </div>

</x-list-layout>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#projetsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
        },
        order: [[0, 'desc']],
        pageLength: 25
    });
});
</script>
@endsection
