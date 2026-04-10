@extends('layouts.app')

@section('title', 'Opérations - Détails')

@section('content')
<div class="container-fluid">

    <div class="mb-3">
        <ul class="nav nav-pills flex-wrap gap-2">
            <li class="nav-item"><a class="nav-link active" href="#infos">Informations</a></li>
            <li class="nav-item"><a class="nav-link" href="#historique">Historique</a></li>
        </ul>
    </div>

    <!-- Carte principale -->
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-file-contract me-2"></i>
                {{ $operation->titre }}
                <span class="badge bg-light text-dark ms-2">{{ $operation->numero_operation }}</span>
            </h5>
        </div>
        <div class="card-body">
            
            <!-- Informations principales -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="text-primary mb-3">Informations générales</h6>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Numéro:</strong></td>
                            <td>{{ $operation->numero_operation }}</td>
                        </tr>
                        <tr>
                            <td><strong>Date:</strong></td>
                            <td>{{ \Carbon\Carbon::parse($operation->date_operation)->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Type:</strong></td>
                            <td>{{ $operation->typeOperation->nom ?? 'Non défini' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Montant:</strong></td>
                            <td class="fw-bold text-primary">{{ number_format($operation->montant, 0, ',', ' ') }} FCFA</td>
                        </tr>
                        <tr>
                            <td><strong>Statut:</strong></td>
                            <td>
                                <span class="badge bg-{{ getOperationStatusColor($operation->statut_courant) }}">
                                    {{ getOperationStatusText($operation->statut_courant) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Priorité:</strong></td>
                            <td>
                                <span class="badge bg-{{ $operation->priorite === 'Haute' ? 'danger' : ($operation->priorite === 'Moyenne' ? 'warning' : 'info') }}">
                                    {{ $operation->priorite }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div class="col-md-6">
                    <h6 class="text-primary mb-3">Description & Détails</h6>
                    <div class="alert alert-info mb-3" style="white-space: pre-wrap; word-wrap: break-word; line-height: 1.6; min-height: 60px;">
                        {{ $operation->description ?: 'Aucune description' }}
                    </div>
                    
                    @if($operation->echeance)
                    <div class="alert alert-info">
                        <i class="fas fa-calendar-alt me-2"></i>
                        <strong>Échéance:</strong> {{ \Carbon\Carbon::parse($operation->echeance)->format('d/m/Y') }}
                        @if($operation->echeance < now() && !in_array($operation->statut_courant, ['termine', 'payee']))
                            <span class="badge bg-danger ms-2">En retard</span>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Client et Service -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="text-primary mb-3">Client</h6>
                    @if($operation->client)
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Nom:</strong></td>
                                <td>{{ $operation->client->nom }}</td>
                            </tr>
                            <tr>
                                <td><strong>Contact:</strong></td>
                                <td>{{ $operation->client->telephone ?? 'Non défini' }}</td>
                            </tr>
                        </table>
                    @else
                        <p class="text-muted">Aucun client associé</p>
                    @endif
                </div>
                
                <div class="col-md-6">
                    <h6 class="text-primary mb-3">Service opérationnel</h6>
                    @if($operation->operationalService)
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Service:</strong></td>
                                <td>{{ $operation->operationalService->nom }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $operation->operationalService->email }}</td>
                            </tr>
                        </table>
                    @else
                        <p class="text-muted">Aucun service associé</p>
                    @endif
                </div>
            </div>

            <!-- Fichiers joints -->
            @if($operation->fichiers && $operation->fichiers->count() > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <h6 class="text-primary mb-3">Fichiers joints</h6>
                    <div class="row">
                        @foreach($operation->fichiers as $fichier)
                        <div class="col-md-4 mb-2">
                            <div class="card">
                                <div class="card-body p-2">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file me-2 text-primary"></i>
                                        <small class="text-truncate flex-grow-1">{{ $fichier->nom_fichier }}</small>
                                        <a href="{{ route('operations.files.download', $fichier->id) }}" 
                                           class="btn btn-sm btn-outline-primary ms-2" title="Télécharger">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="row">
                <div class="col-12">
                    <div class="d-flex gap-2">
                        <a href="{{ route('operations.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                        </a>
                        
                        @if($operation->statut_courant === 'brouillon')
                        <a href="{{ route('operations.edit', $operation->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Modifier
                        </a>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Historique des statuts -->
    <div class="card shadow" id="historique">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">
                <i class="fas fa-history me-2"></i>
                Historique des modifications
            </h5>
        </div>
        <div class="card-body">
            @php
                $logs = \App\Models\OperationStatusLog::where('operation_id', $operation->id)
                    ->orderBy('created_at', 'desc')
                    ->get();
            @endphp
            
            @if($logs->count() > 0)
                <div class="timeline">
                    @foreach($logs as $log)
                    <div class="timeline-item mb-3">
                        <div class="d-flex">
                            <div class="me-3">
                                <div class="badge bg-primary rounded-circle p-2">
                                    <i class="fas fa-exchange-alt"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    Changement de statut
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i') }}</small>
                                </h6>
                                <p class="mb-1">
                                    <span class="badge bg-secondary">{{ $log->from_status }}</span>
                                    <i class="fas fa-arrow-right mx-2"></i>
                                    <span class="badge bg-success">{{ $log->to_status }}</span>
                                </p>
                                @if($log->commentaire)
                                <p class="text-muted mb-0">{{ $log->commentaire }}</p>
                                @endif
                                <small class="text-muted">Par: {{ $log->user_name }}</small>
                            </div>
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
@endsection
