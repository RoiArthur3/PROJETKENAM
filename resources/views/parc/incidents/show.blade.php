@extends('layouts.app')

@section('title', 'Détails de l\'incident')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-triangle-exclamation text-warning me-2"></i>
            Incident #{{ $incident->id }}
        </h1>
        <div class="btn-group" role="group">
            <a href="{{ route('parc.incidents.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>
            <a href="{{ route('parc.incidents.edit', $incident) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> Modifier
            </a>
            <form action="{{ route('parc.incidents.destroy', $incident) }}" 
                  method="POST" 
                  class="d-inline"
                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet incident ?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash me-1"></i> Supprimer
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Informations principales -->
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-info-circle me-2"></i>Informations de l'incident
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong><i class="fas fa-car me-2 text-muted"></i>Véhicule</strong>
                            <p class="ms-4">
                                @if($incident->vehicle)
                                <strong>{{ $incident->vehicle->immatriculation }}</strong><br>
                                <small class="text-muted">{{ $incident->vehicle->marque }} {{ $incident->vehicle->modele }}</small>
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <strong><i class="fas fa-calendar me-2 text-muted"></i>Date de l'incident</strong>
                            <p class="ms-4">{{ $incident->date_incident->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong><i class="fas fa-tag me-2 text-muted"></i>Type</strong>
                            <p class="ms-4">
                                @if($incident->type == 'Accident')
                                <span class="badge bg-danger"><i class="fas fa-car-crash me-1"></i>{{ $incident->type }}</span>
                                @elseif($incident->type == 'Panne')
                                <span class="badge bg-warning text-dark"><i class="fas fa-wrench me-1"></i>{{ $incident->type }}</span>
                                @elseif($incident->type == 'Vol')
                                <span class="badge bg-dark"><i class="fas fa-user-secret me-1"></i>{{ $incident->type }}</span>
                                @elseif($incident->type == 'Vandalisme')
                                <span class="badge bg-secondary"><i class="fas fa-hammer me-1"></i>{{ $incident->type }}</span>
                                @else
                                <span class="badge bg-info"><i class="fas fa-exclamation-circle me-1"></i>{{ $incident->type }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <strong><i class="fas fa-exclamation-triangle me-2 text-muted"></i>Sévérité</strong>
                            <p class="ms-4">
                                @if($incident->severity == 'Critique')
                                <span class="badge bg-danger">{{ $incident->severity }}</span>
                                @elseif($incident->severity == 'Élevée')
                                <span class="badge bg-warning text-dark">{{ $incident->severity }}</span>
                                @elseif($incident->severity == 'Moyenne')
                                <span class="badge bg-info">{{ $incident->severity }}</span>
                                @else
                                <span class="badge bg-secondary">{{ $incident->severity }}</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <strong><i class="fas fa-align-left me-2 text-muted"></i>Description</strong>
                            <p class="ms-4">{{ $incident->description }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong><i class="fas fa-coins me-2 text-muted"></i>Coût estimé</strong>
                            <p class="ms-4">
                                @if($incident->estimated_cost)
                                {{ number_format($incident->estimated_cost, 0, ',', ' ') }} FCFA
                                @else
                                <span class="text-muted">Non renseigné</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <strong><i class="fas fa-money-bill-wave me-2 text-muted"></i>Coût final</strong>
                            <p class="ms-4">
                                @if($incident->final_cost)
                                {{ number_format($incident->final_cost, 0, ',', ' ') }} FCFA
                                @else
                                <span class="text-muted">Non renseigné</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($incident->attachment)
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <strong><i class="fas fa-paperclip me-2 text-muted"></i>Pièce jointe</strong>
                            <p class="ms-4">
                                <a href="{{ asset('storage/' . $incident->attachment) }}" 
                                   target="_blank" 
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-download me-1"></i> Télécharger
                                </a>
                            </p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Panneau latéral -->
        <div class="col-md-4">
            <!-- Statut -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-tasks me-2"></i>Statut
                    </h6>
                </div>
                <div class="card-body text-center">
                    @if($incident->status == 'open')
                    <div class="mb-3">
                        <i class="fas fa-folder-open fa-3x text-danger mb-2"></i>
                        <h5 class="text-danger">Ouvert</h5>
                    </div>
                    @elseif($incident->status == 'in_progress')
                    <div class="mb-3">
                        <i class="fas fa-spinner fa-3x text-warning mb-2"></i>
                        <h5 class="text-warning">En cours</h5>
                    </div>
                    @else
                    <div class="mb-3">
                        <i class="fas fa-check-circle fa-3x text-success mb-2"></i>
                        <h5 class="text-success">Fermé</h5>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Informations complémentaires -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-info me-2"></i>Informations complémentaires
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong><i class="fas fa-user me-2 text-muted"></i>Rapporté par</strong>
                        <p class="ms-4 mb-0">
                            @if($incident->reporter)
                            {{ $incident->reporter->name }}<br>
                            <small class="text-muted">{{ $incident->reporter->email }}</small>
                            @else
                            <span class="text-muted">N/A</span>
                            @endif
                        </p>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <strong><i class="fas fa-calendar-plus me-2 text-muted"></i>Créé le</strong>
                        <p class="ms-4 mb-0">
                            {{ $incident->created_at->format('d/m/Y à H:i') }}<br>
                            <small class="text-muted">{{ $incident->created_at->diffForHumans() }}</small>
                        </p>
                    </div>

                    <hr>

                    <div class="mb-0">
                        <strong><i class="fas fa-calendar-check me-2 text-muted"></i>Modifié le</strong>
                        <p class="ms-4 mb-0">
                            {{ $incident->updated_at->format('d/m/Y à H:i') }}<br>
                            <small class="text-muted">{{ $incident->updated_at->diffForHumans() }}</small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
