@extends('layouts.app')

@section('title', 'Détails de la Requête - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-eye me-2"></i>
                Détails de la Requête
            </h1>
            <p class="text-muted mb-0">Requête #{{ $requete->id }}</p>
        </div>
        <div>
            <a href="{{ route('agent.requetes.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour à la liste
            </a>
        </div>
    </div>

    <!-- Informations principales -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-alt me-2"></i>Informations de la Requête
                    </h6>
                    <div>
                        <span class="badge bg-secondary">
                            {{ $requete->statut_label }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Objet:</strong>
                            <p class="mb-0">{{ $requete->objet }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Type d'opération:</strong>
                            <p class="mb-0">{{ $requete->operation->libelle ?? 'Non spécifié' }}</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Description:</strong>
                        <div class="mt-2 p-3 bg-light rounded">
                            {!! nl2br(e($requete->description)) !!}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <strong>Date de création:</strong>
                            <p class="mb-0">{{ $requete->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Dernière mise à jour:</strong>
                            <p class="mb-0">{{ $requete->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Demandeur:</strong>
                            <p class="mb-0">{{ $requete->demandeur->name ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-info-circle me-2"></i>Statut de validation
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        @if(in_array($requete->statut, ['ENREGISTREE', 'EN_ATTENTE_ENVOI', 'ENVOYEE']))
                            <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                            <h5 class="text-warning">En attente</h5>
                            <p class="text-muted">Votre requête est en attente de traitement.</p>
                        @elseif(in_array($requete->statut, ['EN_COURS_DE_TRAITEMENT', 'TRANSFERE']))
                            <i class="fas fa-spinner fa-3x text-info mb-3"></i>
                            <h5 class="text-info">En cours de traitement</h5>
                            <p class="text-muted">Votre requête est en cours d'examen.</p>
                        @elseif($requete->statut === 'CLOTUREE')
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5 class="text-success">Clôturée</h5>
                            <p class="text-muted">Votre requête a été clôturée.</p>
                        @else
                            <i class="fas fa-times-circle fa-3x text-danger mb-3"></i>
                            <h5 class="text-danger">Rejetée</h5>
                            <p class="text-muted">Votre requête a été rejetée.</p>
                        @endif
                    </div>
                </div>
            </div>

            @if($requete->piecesJointes->count() > 0)
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-paperclip me-2"></i>Fichiers joints
                        </h6>
                    </div>
                    <div class="card-body">
                        @foreach($requete->piecesJointes as $fichier)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small>{{ $fichier->nom_fichier }}</small>
                                <a href="{{ asset('storage/' . $fichier->chemin_fichier) }}"
                                   target="_blank"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Historique -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-history me-2"></i>Historique
            </h6>
        </div>
        <div class="card-body">
            @if($requete->historiques->count() > 0)
                <div class="timeline">
                    @foreach($requete->historiques->sortBy('created_at') as $h)
                        <div class="timeline-item mb-3">
                            <div class="timeline-marker">
                                <i class="fas fa-history text-primary"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>{{ $h->user->name ?? '-' }}</strong>
                                        <span class="badge bg-secondary ms-2">
                                            {{ $h->action }}
                                        </span>
                                    </div>
                                    <small class="text-muted">{{ $h->created_at->format('d/m/Y H:i') }}</small>
                                </div>
                                @if($h->commentaire)
                                    <div class="mt-2 p-2 bg-light rounded">
                                        <small>{{ $h->commentaire }}</small>
                                    </div>
                                @endif
                                @if($h->ancien_statut || $h->nouveau_statut)
                                    <div class="mt-2">
                                        <small class="text-muted">Statut: {{ $h->ancien_statut ?? '-' }} → {{ $h->nouveau_statut ?? '-' }}</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-2x text-gray-300 mb-2"></i>
                    <p class="text-muted">Aucun historique enregistré pour le moment.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: #e9ecef;
}

.timeline-item {
    position: relative;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 0;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background-color: white;
    border: 2px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
}

.timeline-content {
    margin-left: 10px;
}
</style>

@endsection
