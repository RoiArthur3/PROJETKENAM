@extends('layouts.app')

@section('title', 'Validation d\'Opération | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h5 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clipboard-check me-2"></i>Validation de l'Opération
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Informations de l'opération -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">Détails de l'Opération</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Titre:</strong> {{ $operation->titre }}</p>
                            @if(isset($operation->montant))
                                <p><strong>Montant:</strong>
                                    @php
                                        $montant = is_numeric($operation->montant) ? $operation->montant : 0;
                                        $montantFormate = number_format($montant, 0, ',', ' ') . ' FCFA';
                                        try {
                                            $montantEnLettres = \App\Helpers\OperationHelper::numberToWords($montant);
                                            if (empty($montantEnLettres)) {
                                                $montantEnLettres = 'zéro';
                                            }
                                        } catch (\Exception $e) {
                                            $montantEnLettres = 'zéro';
                                        }
                                    @endphp

                                    @if($montant == 0)
                                        {{ $montantFormate }} ({{ $montantEnLettres }} Francs CFA)
                                    @else
                                        {{ $montantFormate }}
                                        <br>
                                        <small class="text-muted">
                                            ({{ ucfirst($montantEnLettres) }} Francs CFA)
                                        </small>
                                    @endif
                                </p>
                            @endif
                                <p><strong>Priorité:</strong>
                                    <span class="badge bg-{{ $operation->priorite === 'urgente' ? 'danger' : ($operation->priorite === 'haute' ? 'warning' : 'info') }}">
                                        {{ ucfirst($operation->priorite) }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Demandeur:</strong> {{ $operation->demandeur_name }}</p>
                                <p><strong>Email:</strong> {{ $operation->demandeur_email }}</p>
                                <p><strong>Date:</strong> {{ $operation->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        @if($operation->description)
                            <div class="mt-3">
                                <p><strong>Description:</strong></p>
                                <p>{{ $operation->description }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Étapes de validation -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">Circuit de Validation</h6>
                        <div class="timeline">
                            @foreach($validationSteps as $index => $validationStep)
                                <div class="timeline-item {{ $validationStep->statut == 'EN_COURS' ? 'active' : ($validationStep->statut == 'VALIDE' ? 'completed' : 'pending') }}">
                                    <div class="timeline-marker">
                                        @if($validationStep->statut == 'VALIDE')
                                            <i class="fas fa-check text-success"></i>
                                        @elseif($validationStep->statut == 'EN_COURS')
                                            <i class="fas fa-clock text-warning"></i>
                                        @else
                                            <i class="fas fa-circle text-muted"></i>
                                        @endif
                                    </div>
                                    <div class="timeline-content">
                                        <h6>Étape {{ $validationStep->ordre_validation }}</h6>
                                        <p class="mb-1">Service: {{ \App\Models\ServiceOperationnel::find($validationStep->service_operationnel_id)?->nom ?? 'Service inconnu' }}</p>
                                        <small class="text-muted">Statut: {{ $validationStep->statut }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Pièces jointes -->
                    @if($operation->fichiers && $operation->fichiers->count() > 0)
                        <div class="mb-4">
                            <h6 class="text-primary mb-3">Pièces Jointes</h6>
                            <div class="row">
                                @foreach($operation->fichiers as $fichier)
                                    <div class="col-md-6 mb-2">
                                        <div class="d-flex align-items-center p-2 border rounded">
                                            <i class="{{ $fichier->icone }} me-2 text-primary"></i>
                                            <div class="flex-grow-1">
                                                <small class="d-block">{{ $fichier->nom_original }}</small>
                                                <small class="text-muted">{{ $fichier->taille_formatee }}</small>
                                            </div>
                                            <a href="{{ $fichier->url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Formulaire de validation -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">Action de Validation</h6>
                        <form method="POST" action="{{ request()->fullUrl() }}">
                            @csrf
                            <div class="mb-3">
                                <label for="commentaire" class="form-label">Commentaire (optionnel)</label>
                                <textarea name="commentaire" id="commentaire" class="form-control" rows="3" placeholder="Ajoutez un commentaire..."></textarea>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" name="action" value="approve" class="btn btn-success">
                                    <i class="fas fa-check me-1"></i>Approuver
                                </button>
                                <button type="submit" name="action" value="reject" class="btn btn-danger">
                                    <i class="fas fa-times me-1"></i>Rejeter
                                </button>
                                <a href="{{ route('operations.show', $operation->id) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>Retour
                                </a>
                            </div>
                        </form>
                    </div>
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
    left: -30px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: white;
    border: 2px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
}

.timeline-item.active .timeline-marker {
    border-color: #ffc107;
}

.timeline-item.completed .timeline-marker {
    border-color: #28a745;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    border-left: 3px solid #e9ecef;
}

.timeline-item.active .timeline-content {
    border-left-color: #ffc107;
    background: #fff3cd;
}

.timeline-item.completed .timeline-content {
    border-left-color: #28a745;
    background: #d4edda;
}
</style>
@endsection
