@extends('layouts.app')

@section('title', 'Réparations - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-wrench me-2 text-warning"></i>Réparations Véhicules
            </h1>
            <p class="text-muted mb-0">Suivi des pannes et réparations</p>
        </div>
        <a href="{{ route('parc.reparations.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouvelle Réparation
        </a>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-start border-warning border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Total Réparations</div>
                    <div class="h3 mb-0 text-warning">{{ $reparations->total() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-danger border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">En Attente</div>
                    <div class="h3 mb-0 text-danger">{{ $reparations->where('statut', 'En attente')->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-info border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">En Cours</div>
                    <div class="h3 mb-0 text-info">{{ $reparations->where('statut', 'En cours')->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Terminées</div>
                    <div class="h3 mb-0 text-success">{{ $reparations->where('statut', 'Terminée')->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Liste des Réparations
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="reparationsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Véhicule</th>
                            <th>Type Panne</th>
                            <th>Urgence</th>
                            <th>Garage</th>
                            <th>Coût Estimé</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reparations as $reparation)
                            <tr>
                                <td>{{ $reparation->date->format('d/m/Y') }}</td>
                                <td class="fw-semibold">
                                    {{ $reparation->vehicule ? $reparation->vehicule->immatriculation : 'N/A' }}
                                </td>
                                <td><span class="badge bg-secondary">{{ $reparation->type_panne }}</span></td>
                                <td>
                                    @php
                                        $urgenceBadge = [
                                            'Normale' => 'secondary',
                                            'Urgente' => 'warning',
                                            'Critique' => 'danger',
                                        ][$reparation->urgence] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $urgenceBadge }}">{{ $reparation->urgence }}</span>
                                </td>
                                <td>{{ $reparation->garage ?? '—' }}</td>
                                <td class="text-end">
                                    {{ $reparation->cout_estime ? number_format($reparation->cout_estime, 0, ',', ' ') . ' FCFA' : '—' }}
                                </td>
                                <td>
                                    @php
                                        $statutBadge = [
                                            'En attente' => 'danger',
                                            'En cours' => 'info',
                                            'Terminée' => 'success',
                                            'Annulée' => 'secondary',
                                        ][$reparation->statut] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $statutBadge }}">{{ $reparation->statut }}</span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-info" title="Voir détails" data-bs-toggle="modal" data-bs-target="#detailModal{{ $reparation->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal Détails -->
                            <div class="modal fade" id="detailModal{{ $reparation->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title">Détails Réparation #{{ $reparation->id }}</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <dl class="row">
                                                        <dt class="col-sm-5">Date panne:</dt>
                                                        <dd class="col-sm-7">{{ $reparation->date->format('d/m/Y') }}</dd>
                                                        
                                                        <dt class="col-sm-5">Véhicule:</dt>
                                                        <dd class="col-sm-7">
                                                            @if($reparation->vehicule)
                                                                {{ $reparation->vehicule->immatriculation }}<br>
                                                                <small>{{ $reparation->vehicule->marque }} {{ $reparation->vehicule->modele }}</small>
                                                            @else
                                                                N/A
                                                            @endif
                                                        </dd>
                                                        
                                                        <dt class="col-sm-5">Type panne:</dt>
                                                        <dd class="col-sm-7"><span class="badge bg-secondary">{{ $reparation->type_panne }}</span></dd>
                                                        
                                                        <dt class="col-sm-5">Urgence:</dt>
                                                        <dd class="col-sm-7"><span class="badge bg-{{ $urgenceBadge }}">{{ $reparation->urgence }}</span></dd>
                                                    </dl>
                                                </div>
                                                <div class="col-md-6">
                                                    <dl class="row">
                                                        <dt class="col-sm-5">Garage:</dt>
                                                        <dd class="col-sm-7">{{ $reparation->garage ?? 'Non renseigné' }}</dd>
                                                        
                                                        <dt class="col-sm-5">Coût estimé:</dt>
                                                        <dd class="col-sm-7">{{ $reparation->cout_estime ? number_format($reparation->cout_estime, 0, ',', ' ') . ' FCFA' : 'Non renseigné' }}</dd>
                                                        
                                                        <dt class="col-sm-5">Coût réel:</dt>
                                                        <dd class="col-sm-7">{{ $reparation->cout_reel ? number_format($reparation->cout_reel, 0, ',', ' ') . ' FCFA' : 'Non renseigné' }}</dd>
                                                        
                                                        <dt class="col-sm-5">Statut:</dt>
                                                        <dd class="col-sm-7"><span class="badge bg-{{ $statutBadge }}">{{ $reparation->statut }}</span></dd>
                                                    </dl>
                                                </div>
                                            </div>
                                            
                                            @if($reparation->description)
                                                <hr>
                                                <h6 class="fw-bold">Description de la panne:</h6>
                                                <p class="text-muted">{{ $reparation->description }}</p>
                                            @endif
                                            
                                            @if($reparation->notes)
                                                <hr>
                                                <h6 class="fw-bold">Notes:</h6>
                                                <p class="text-muted">{{ $reparation->notes }}</p>
                                            @endif
                                            
                                            @if($reparation->date_debut || $reparation->date_fin)
                                                <hr>
                                                <dl class="row">
                                                    @if($reparation->date_debut)
                                                        <dt class="col-sm-3">Date début:</dt>
                                                        <dd class="col-sm-3">{{ $reparation->date_debut->format('d/m/Y') }}</dd>
                                                    @endif
                                                    @if($reparation->date_fin)
                                                        <dt class="col-sm-3">Date fin:</dt>
                                                        <dd class="col-sm-3">{{ $reparation->date_fin->format('d/m/Y') }}</dd>
                                                    @endif
                                                </dl>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    Aucune réparation enregistrée
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($reparations->hasPages())
                <div class="mt-3">
                    {{ $reparations->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#reparationsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
        },
        order: [[0, 'desc']],
        pageLength: 25
    });
});
</script>
@endsection
