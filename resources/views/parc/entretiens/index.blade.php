@extends('layouts.app')

@section('title', 'Entretiens Véhicules - KENAM SERVICES')

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
                <i class="fas fa-wrench me-2 text-warning"></i>Entretiens Véhicules
            </h1>
            <p class="text-muted mb-0">Planification et suivi des entretiens</p>
        </div>
        <a href="{{ route('parc.entretiens.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouvel Entretien
        </a>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-start border-warning border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Entretiens Planifiés</div>
                    <div class="h3 mb-0 text-warning">{{ $entretiens->where('statut', 'Planifié')->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-info border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">En Cours</div>
                    <div class="h3 mb-0 text-info">{{ $entretiens->where('statut', 'En cours')->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Terminés</div>
                    <div class="h3 mb-0 text-success">{{ $entretiens->where('statut', 'Terminé')->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-danger border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Annulés</div>
                    <div class="h3 mb-0 text-danger">{{ $entretiens->where('statut', 'Annulé')->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Liste des Entretiens
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="entretiensTable">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Véhicule</th>
                            <th>Type</th>
                            <th>Kilométrage</th>
                            <th>Prestataire</th>
                            <th>Coût</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entretiens as $entretien)
                            <tr>
                                <td>{{ $entretien->date->format('d/m/Y') }}</td>
                                <td class="fw-semibold">
                                    {{ $entretien->vehicule ? $entretien->vehicule->immatriculation : 'N/A' }}
                                    @if($entretien->vehicule)
                                        <br><small>{{ $entretien->vehicule->marque }} {{ $entretien->vehicule->modele }}</small>
                                    @endif
                                </td>
                                <td><span class="badge bg-secondary">{{ $entretien->type }}</span></td>
                                <td class="text-end">{{ $entretien->kilometrage ? number_format($entretien->kilometrage, 0, ',', ' ') . ' km' : '—' }}</td>
                                <td>{{ $entretien->prestataire ?? '—' }}</td>
                                <td class="text-end">
                                    {{ $entretien->cout ? number_format($entretien->cout, 0, ',', ' ') . ' FCFA' : '—' }}
                                </td>
                                <td>
                                    @php
                                        $statutBadge = [
                                            'Planifié' => 'warning',
                                            'En cours' => 'info',
                                            'Terminé' => 'success',
                                            'Annulé' => 'secondary',
                                        ][$entretien->statut] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $statutBadge }}">{{ $entretien->statut }}</span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-info" title="Voir détails" data-bs-toggle="modal" data-bs-target="#detailModal{{ $entretien->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($entretiens->hasPages())
                <div class="mt-3">
                    {{ $entretiens->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Modals rendus en dehors du tableau pour éviter les erreurs DataTables --}}
@foreach($entretiens as $entretien)
<div class="modal fade" id="detailModal{{ $entretien->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Détails Entretien #{{ $entretien->id }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-5">Date:</dt>
                            <dd class="col-sm-7">{{ $entretien->date->format('d/m/Y') }}</dd>

                            <dt class="col-sm-5">Véhicule:</dt>
                            <dd class="col-sm-7">
                                @if($entretien->vehicule)
                                    {{ $entretien->vehicule->immatriculation }}<br>
                                    <small>{{ $entretien->vehicule->marque }} {{ $entretien->vehicule->modele }}</small>
                                @else
                                    N/A
                                @endif
                            </dd>

                            <dt class="col-sm-5">Type:</dt>
                            <dd class="col-sm-7"><span class="badge bg-secondary">{{ $entretien->type }}</span></dd>

                            <dt class="col-sm-5">Kilométrage:</dt>
                            <dd class="col-sm-7">{{ $entretien->kilometrage ? number_format($entretien->kilometrage, 0, ',', ' ') . ' km' : 'Non renseigné' }}</dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-5">Prestataire:</dt>
                            <dd class="col-sm-7">{{ $entretien->prestataire ?? 'Non renseigné' }}</dd>

                            <dt class="col-sm-5">Coût:</dt>
                            <dd class="col-sm-7">{{ $entretien->cout ? number_format($entretien->cout, 0, ',', ' ') . ' FCFA' : 'Non renseigné' }}</dd>

                            <dt class="col-sm-5">Statut:</dt>
                            <dd class="col-sm-7"><span class="badge bg-{{ [
                                'Planifié' => 'warning',
                                'En cours' => 'info',
                                'Terminé' => 'success',
                                'Annulé' => 'secondary',
                            ][$entretien->statut] ?? 'secondary' }}">{{ $entretien->statut }}</span></dd>

                            @if($entretien->user)
                                <dt class="col-sm-5">Créé par:</dt>
                                <dd class="col-sm-7">{{ $entretien->user->name }}</dd>
                            @endif
                        </dl>
                    </div>
                </div>

                @if($entretien->description)
                    <hr>
                    <h6 class="fw-bold">Description:</h6>
                    <p class="text-muted">{{ $entretien->description }}</p>
                @endif

                <dl class="row">
                    <dt class="col-sm-3">Créé le:</dt>
                    <dd class="col-sm-3">{{ $entretien->created_at->format('d/m/Y H:i') }}</dd>
                    <dt class="col-sm-3">Dernière modif:</dt>
                    <dd class="col-sm-3">{{ $entretien->updated_at->format('d/m/Y H:i') }}</dd>
                </dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#entretiensTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
        },
        order: [[0, 'desc']],
        pageLength: 25
    });
});
</script>
@endsection
