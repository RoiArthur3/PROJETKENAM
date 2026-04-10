<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation Opération #{{ $operation->id }} - KENAM SERVICES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .validation-container {
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
        .btn-success {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
        }
        .btn-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border: none;
        }
        .badge {
            padding: 0.5rem 1rem;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container validation-container">
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
                        
                        <!-- Informations de l'opération -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-info-circle me-2"></i>Informations de l'opération
                            </h5>
                            <div class="row">
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
                                        <span class="badge bg-info">{{ $operation->statut_courant ?? 'En cours' }}</span>
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
                        </div>

                        <!-- Formulaire de validation -->
                        @if($currentStep && $operation->statut_courant !== 'approuvee' && $operation->statut_courant !== 'rejetee')
                            <div class="alert alert-warning border-0 bg-gradient" style="background: linear-gradient(135deg, #fff3cd, #ffeaa7);">
                                <h6 class="mb-2">
                                    <i class="fas fa-gavel me-2"></i>Action de validation requise
                                </h6>
                                <p class="mb-0">
                                    Vous êtes le validateur actuel pour cette opération (Étape {{ $currentStep->ordre_validation ?? 1 }}). 
                                    Veuillez examiner les détails ci-dessus et prendre une décision.
                                </p>
                            </div>

                            <div class="card border-primary">
                                <div class="card-body">
                                    <form method="POST" action="{{ route('operations.approve', ['operation' => $operation->id, 'step' => $currentStep->ordre_validation ?? 1]) }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="commentaire" class="form-label">
                                                <i class="fas fa-comment me-2"></i>Commentaire (optionnel)
                                            </label>
                                            <textarea class="form-control" id="commentaire" name="commentaire" rows="3"
                                                      placeholder="Ajoutez votre commentaire ou remarque..."></textarea>
                                        </div>

                                        <div class="d-flex flex-wrap gap-2">
                                            <button type="submit" class="btn btn-success btn-lg">
                                                <i class="fas fa-check me-2"></i>Approuver
                                            </button>
                                            <a href="{{ route('operations.reject', ['operation' => $operation->id, 'step' => $currentStep->ordre_validation ?? 1]) }}" 
                                               class="btn btn-danger btn-lg">
                                                <i class="fas fa-times me-2"></i>Rejeter
                                            </a>
                                            <a href="{{ route('operations.show', $operation->id) }}" class="btn btn-secondary btn-lg">
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
                                <a href="{{ route('operations.show', $operation->id) }}" class="btn btn-primary btn-lg">
                                    <i class="fas fa-eye me-2"></i>Voir les détails de l'opération
                                </a>
                                <a href="{{ url('/operations') }}" class="btn btn-outline-secondary btn-lg ms-2">
                                    <i class="fas fa-list me-2"></i>Retour aux opérations
                                </a>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
