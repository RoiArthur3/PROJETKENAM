@php
function traduireStatut($statut) {
    $traductions = [
        'pending' => 'En attente',
        'approved' => 'Approuvé',
        'rejected' => 'Rejeté',
        'EN_COURS' => 'En cours',
        'EN_ATTENTE' => 'En attente',
        'VALIDE' => 'Validé',
        'REJETE' => 'Rejeté'
    ];
    return $traductions[$statut] ?? $statut;
}
@endphp

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exécution Opération #{{ $operation->id }} - KENAM SERVICES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .execution-container {
            padding: 2rem 0;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            border-radius: 15px 15px 0 0 !important;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .badge {
            padding: 0.5rem 1rem;
            font-weight: 500;
        }
        .step-item {
            border-left: 4px solid #dee2e6;
            padding-left: 1rem;
            margin-bottom: 1rem;
        }
        .step-item.active {
            border-left-color: #28a745;
        }
        .step-item.completed {
            border-left-color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container execution-container">
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
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-play-circle me-2"></i>
                            Exécution Opération #{{ $operation->id }}
                        </h4>
                    </div>
                    <div class="card-body">
                        
                        <!-- Navigation -->
                        <nav aria-label="breadcrumb" class="mb-4">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/operations') }}" class="text-decoration-none">Opérations</a></li>
                                <li class="breadcrumb-item"><a href="{{ url('/operations/' . $operation->id) }}" class="text-decoration-none">#{{ $operation->id }}</a></li>
                                <li class="breadcrumb-item active">Exécution</li>
                            </ol>
                        </nav>
                        
                        <!-- Informations de l'opération -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <strong>Titre :</strong><br>
                                    <span class="text-muted">{{ $operation->titre }}</span>
                                </div>
                                <div class="mb-3">
                                    <strong>Description :</strong><br>
                                    <span class="text-muted">{{ $operation->description ?? 'Non spécifiée' }}</span>
                                </div>
                                <div class="mb-3">
                                    <strong>Montant :</strong><br>
                                    <span class="badge bg-warning text-dark">{{ number_format($operation->montant ?? 0, 0, ',', ' ') }} FCFA</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <strong>Statut :</strong><br>
                                    <span class="badge bg-info">{{ traduireStatut($operation->statut_courant ?? 'En cours') }}</span>
                                </div>
                                <div class="mb-3">
                                    <strong>Demandeur :</strong><br>
                                    <span class="text-muted">{{ $operation->demandeur_name ?? 'Non spécifié' }}</span>
                                </div>
                                <div class="mb-3">
                                    <strong>Date de création :</strong><br>
                                    <span class="text-muted">{{ $operation->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Étapes de validation -->
                        @if($steps && $steps->count() > 0)
                            <div class="card">
                                <div class="card-header bg-secondary text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-tasks me-2"></i>Étapes de validation
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @foreach($steps as $step)
                                        <div class="step-item {{ $step->statut === 'EN_COURS' ? 'active' : ($step->statut === 'APPROUVE' ? 'completed' : '') }}">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1">Étape {{ $step->ordre_validation }} - {{ $step->service_name }}</h6>
                                                    <small class="text-muted">{{ $step->service_email }}</small>
                                                </div>
                                                <div>
                                                    <span class="badge bg-{{
                                                        $step->statut === 'APPROUVE' ? 'success' :
                                                        ($step->statut === 'EN_COURS' ? 'warning' :
                                                        ($step->statut === 'REJETE' ? 'danger' : 'secondary'))
                                                    }} text-white">
                                                        {{ traduireStatut($step->statut) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="text-center mt-4">
                            <a href="{{ url('/operations/' . $operation->id) }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-eye me-2"></i>Voir les détails complets
                            </a>
                            <a href="{{ url('/operations') }}" class="btn btn-outline-secondary btn-lg ms-2">
                                <i class="fas fa-list me-2"></i>Retour aux opérations
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
