@extends('layouts.guest')

@section('title', 'Validation Opération #' . $operation->id . ' - KENAM SERVICES')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Messages flash -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Carte principale -->
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-clipboard-check me-2"></i>
                        Validation Opération #{{ $operation->id }}
                    </h4>
                </div>
                <div class="card-body">
                    
                    <!-- Navigation -->
                    <nav aria-label="breadcrumb" class="mb-4">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/operations') }}" class="text-decoration-none">Opérations</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('/operations/' . $operation->id) }}" class="text-decoration-none">#{{ $operation->id }}</a></li>
                            <li class="breadcrumb-item active">Validation</li>
                        </ol>
                    </nav>
                    
                    <!-- Statut actuel -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card shadow border-left-{{
                                $operation->statut_courant == 'approuvee' ? 'success' :
                                ($operation->statut_courant == 'rejetee' ? 'danger' :
                                ($operation->statut_courant == 'en_validation' ? 'warning' : 'info'))
                            }}">
                                <div class="card-body py-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0">Statut actuel</h6>
                                            <span class="badge bg-{{
                                                $operation->statut_courant == 'approuvee' ? 'success' :
                                                ($operation->statut_courant == 'rejetee' ? 'danger' :
                                                ($operation->statut_courant == 'en_validation' ? 'warning' : 'info'))
                                            }} text-white">
                                                {{ strtoupper($operation->statut_courant ?? 'EN COURS') }}
                                            </span>
                                        </div>
                                        @if($operation->priorite === 'urgente')
                                            <span class="badge bg-danger text-white">
                                                <i class="fas fa-exclamation-triangle me-1"></i>URGENT
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Informations de l'opération -->
                    <div class="row">
                        <div class="col-lg-8 mb-4">
                            <div class="card shadow">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-file-alt me-2"></i>Détails de la requête
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Titre:</strong> {{ $operation->titre }}</p>
                                            <p><strong>Description:</strong> {{ $operation->description ?: 'Non spécifiée' }}</p>
                                            <p><strong>Montant:</strong>
                                                <span class="text-success fw-bold">{{ number_format($operation->montant ?? 0, 0, ',', ' ') }} FCFA</span>
                                            </p>
                                            <p><strong>Priorité:</strong>
                                                <span class="badge bg-{{
                                                    $operation->priorite === 'urgente' ? 'danger' :
                                                    ($operation->priorite === 'haute' ? 'warning' :
                                                    ($operation->priorite === 'moyenne' ? 'info' : 'secondary'))
                                                }}">
                                                    {{ ucfirst($operation->priorite ?? 'normale') }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Demandeur:</strong> {{ $operation->demandeur_name ?? 'Non spécifié' }} ({{ $operation->demandeur_email ?? 'Non spécifié' }})</p>
                                            <p><strong>Date de soumission:</strong> {{ $operation->created_at->format('d/m/Y H:i') }}</p>
                                            @if($operation->echeance)
                                                <p><strong>Échéance:</strong> {{ $operation->echeance->format('d/m/Y') }}</p>
                                            @endif
                                            <p><strong>Type d'opération:</strong> {{ $operation->type->libelle ?? 'Non spécifié' }}</p>
                                        </div>
                                    </div>

                                    @if($operation->fichiers && $operation->fichiers->count() > 0)
                                        <hr>
                                        <h6><i class="fas fa-paperclip me-2"></i>Pièces jointes</h6>
                                        <div class="row">
                                            @foreach($operation->fichiers as $fichier)
                                                <div class="col-md-6 mb-2">
                                                    <a href="{{ asset('storage/' . $fichier->chemin) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-download me-1"></i>{{ $fichier->nom_original }}
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Formulaire de validation -->
                            @if($currentStep && $currentStep->statut === 'EN_COURS' && $operation->statut_courant !== 'approuvee' && $operation->statut_courant !== 'rejetee')
                                <div class="card shadow">
                                    <div class="card-header bg-warning text-dark">
                                        <h5 class="mb-0">
                                            <i class="fas fa-gavel me-2"></i>Action de validation - Étape {{ $currentStep->ordre_validation }}
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted mb-3">
                                            Vous êtes le validateur actuel pour cette opération. Veuillez examiner les détails ci-dessus et prendre une décision.
                                        </p>

                                        <form method="POST" action="{{ route('operations.approve', ['operation' => $operation->id, 'step' => $currentStep->ordre_validation]) }}">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="commentaire" class="form-label">Commentaire (optionnel)</label>
                                                <textarea class="form-control" id="commentaire" name="commentaire" rows="3"
                                                          placeholder="Ajoutez votre commentaire ou remarque..."></textarea>
                                            </div>

                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-check me-2"></i>Approuver
                                                </button>
                                                <a href="{{ route('operations.reject', ['operation' => $operation->id, 'step' => $currentStep->ordre_validation]) }}" 
                                                   class="btn btn-danger">
                                                    <i class="fas fa-times me-2"></i>Rejeter
                                                </a>
                                                <a href="{{ route('operations.show', $operation->id) }}" class="btn btn-secondary">
                                                    <i class="fas fa-eye me-2"></i>Voir détails complets
                                                </a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Cette opération a déjà été traitée ou n'est pas accessible pour validation.
                                </div>
                                <div class="text-center">
                                    <a href="{{ route('operations.show', $operation->id) }}" class="btn btn-primary">
                                        <i class="fas fa-eye me-2"></i>Voir les détails de l'opération
                                    </a>
                                    <a href="{{ url('/operations') }}" class="btn btn-outline-secondary ms-2">
                                        <i class="fas fa-list me-2"></i>Retour aux opérations
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Colonne de droite -->
                        <div class="col-lg-4">
                            <!-- Étapes de validation -->
                            @if($allSteps && $allSteps->count() > 0)
                                <div class="card shadow mb-4">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-tasks me-2"></i>Étapes de validation
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @foreach($allSteps as $step)
                                            <div class="d-flex align-items-center mb-3 p-2 rounded {{ $step->statut === 'EN_COURS' ? 'bg-warning bg-opacity-25' : ($step->statut === 'APPROUVE' ? 'bg-success bg-opacity-25' : 'bg-light') }}">
                                                <div class="me-3">
                                                    <div class="rounded-circle bg-{{ $step->statut === 'EN_COURS' ? 'warning' : ($step->statut === 'APPROUVE' ? 'success' : 'secondary') }} text-white d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                                        {{ $step->ordre_validation }}
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="fw-bold">{{ $step->service_name }}</div>
                                                    <small class="text-muted">{{ $step->service_email }}</small>
                                                </div>
                                                <div>
                                                    @if($step->statut === 'APPROUVE')
                                                        <i class="fas fa-check-circle text-success"></i>
                                                    @elseif($step->statut === 'EN_COURS')
                                                        <i class="fas fa-clock text-warning"></i>
                                                    @else
                                                        <i class="fas fa-circle text-secondary"></i>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Actions rapides -->
                            <div class="card shadow">
                                <div class="card-header bg-secondary text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-bolt me-2"></i>Actions rapides
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('operations.show', $operation->id) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye me-2"></i>Voir les détails
                                        </a>
                                        <a href="{{ url('/operations') }}" class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-list me-2"></i>Liste des opérations
                                        </a>
                                        <a href="{{ url('/dashboard') }}" class="btn btn-outline-info btn-sm">
                                            <i class="fas fa-tachometer-alt me-2"></i>Tableau de bord
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
