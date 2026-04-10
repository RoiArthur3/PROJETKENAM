@extends('layouts.app')

@section('title', 'Suivi d\'Opération - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Suivi d'Opération</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('operations.my.requests') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
            @if ($operation->statut_courant === 'brouillon')
                <a href="{{ route('operations.edit', $operation->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Modifier
                </a>
            @endif
        </div>
    </div>

    <!-- Informations principales -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ $operation->titre }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Description:</strong></p>
                            <p>{{ $operation->description ?: 'Aucune description' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Informations générales:</strong></p>
                            <ul class="list-unstyled">
                                <li><strong>Montant:</strong> {{ number_format($operation->montant ?? 0, 0, ',', ' ') }} FCFA</li>
                                <li><strong>Priorité:</strong> 
                                    <span class="badge bg-{{ $operation->priorite === 'urgente' ? 'danger' : ($operation->priorite === 'haute' ? 'warning' : ($operation->priorite === 'moyenne' ? 'info' : 'secondary')) }}">
                                        {{ $operation->priorite }}
                                    </span>
                                </li>
                                <li><strong>Statut:</strong> 
                                    <span class="badge bg-{{ $operation->statut_courant === 'en_attente' ? 'warning' : ($operation->statut_courant === 'en_cours' ? 'info' : ($operation->statut_courant === 'termine' ? 'success' : 'danger')) }}">
                                        {{ $operation->statut_courant }}
                                    </span>
                                </li>
                                <li><strong>Date:</strong> {{ $operation->date_operation->format('d/m/Y') }}</li>
                                <li><strong>Échéance:</strong> {{ $operation->echeance ? $operation->echeance->format('d/m/Y') : '-' }}</li>
                            </ul>
                        </div>
                    </div>
                    
                    @if ($operation->client || $operation->operationalService || $operation->type)
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <p><strong>Détails:</strong></p>
                                <ul class="list-unstyled">
                                    @if ($operation->client)
                                        <li><strong>Client:</strong> {{ $operation->client->nom }}</li>
                                    @endif
                                    @if ($operation->operationalService)
                                        <li><strong>Service:</strong> {{ $operation->operationalService->nom }}</li>
                                    @endif
                                    @if ($operation->typeOperation)
                                        <p><strong>Type d'opération:</strong> {{ $operation->typeOperation->libelle ?? 'Non spécifié' }}</p>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Demandeur</h5>
                </div>
                <div class="card-body">
                    <p><strong>Nom:</strong> {{ $operation->demandeur_name }}</p>
                    <p><strong>Email:</strong> {{ $operation->demandeur_email }}</p>
                    <p><strong>Créé le:</strong> {{ $operation->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Dernière modification:</strong> {{ $operation->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Historique des statuts -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Historique des statuts</h5>
                </div>
                <div class="card-body">
                    @if ($statusHistory->count() > 0)
                        <div class="timeline">
                            @foreach ($statusHistory as $log)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-{{ $log->to_status === 'termine' ? 'success' : ($log->to_status === 'en_cours' ? 'info' : ($log->to_status === 'en_attente' ? 'warning' : 'danger')) }}"></div>
                                    <div class="timeline-content">
                                        <h6>{{ $log->to_status }}</h6>
                                        <p class="text-muted">{{ $log->commentaire }}</p>
                                        <small class="text-muted">
                                            {{ $log->user_name }} - {{ $log->created_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">Aucun historique disponible</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Étapes de validation</h5>
                </div>
                <div class="card-body">
                    @if ($operation->validations->count() > 0)
                        @foreach ($operation->validations->sortBy('ordre_validation') as $validation)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>{{ $validation->serviceOperationnel->nom }}</span>
                                <span class="badge bg-{{ $validation->statut === 'VALIDE' ? 'success' : ($validation->statut === 'EN_COURS' ? 'info' : ($validation->statut === 'EN_ATTENTE' ? 'warning' : 'danger')) }}">
                                    {{ $validation->statut }}
                                </span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">Aucune étape de validation définie</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Documents et commentaires -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Documents</h5>
                </div>
                <div class="card-body">
                    @if ($operation->documents->count() > 0)
                        @foreach ($operation->documents as $document)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <a href="{{ asset('storage/' . $document->chemin) }}" target="_blank">
                                    <i class="fas fa-file me-2"></i>{{ $document->nom }}
                                </a>
                                <small class="text-muted">{{ $document->taille ? number_format($document->taille / 1024, 2) . ' KB' : '-' }}</small>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">Aucun document attaché</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Commentaires</h5>
                </div>
                <div class="card-body">
                    @if ($operation->comments->count() > 0)
                        @foreach ($operation->comments->latest()->take(5)->get() as $comment)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <strong>{{ $comment->user->name }}</strong>
                                    <small class="text-muted">{{ $comment->created_at->format('d/m/Y H:i') }}</small>
                                </div>
                                <p class="mb-0">{{ $comment->commentaire }}</p>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">Aucun commentaire</p>
                    @endif
                </div>
            </div>
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
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
}

.timeline-content {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 4px;
}
</style>
@endsection
