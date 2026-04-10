@extends('layouts.app')

@section('title', 'Détails Assurance | KENAM SERVICES')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="mb-0">
                    <i class="fas fa-shield-alt text-primary me-2"></i>
                    {{ $assurance->numero_police }}
                </h1>
                <div>
                    <a href="{{ route('materiel.assurances.edit', $assurance) }}" class="btn btn-warning me-2">
                        <i class="fas fa-edit me-2"></i>Modifier
                    </a>
                    <a href="{{ route('materiel.assurances.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Détails de l'Assurance</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>N° de Police:</strong>
                            <p class="text-muted">{{ $assurance->numero_police }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Assureur:</strong>
                            <p class="text-muted">{{ $assurance->assureur }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Engin:</strong>
                            <p class="text-muted">
                                @if($assurance->vehicle)
                                    <a href="{{ route('materiel.vehicules.show', $assurance->vehicle) }}" class="text-decoration-none">
                                        {{ $assurance->vehicle->immatriculation }} - {{ $assurance->vehicle->marque }} {{ $assurance->vehicle->modele }}
                                    </a>
                                @else
                                    <span>Non assignée</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <strong>Prime Annuelle:</strong>
                            <p class="text-muted">
                                @if($assurance->prime_annuelle)
                                    {{ number_format($assurance->prime_annuelle, 2, ',', ' ') }} DZD
                                @else
                                    Non spécifiée
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Date de Début:</strong>
                            <p class="text-muted">{{ $assurance->date_debut->format('d/m/Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Date d'Expiration:</strong>
                            <p class="text-muted">{{ $assurance->date_fin->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    @if($assurance->notes)
                    <div class="row mb-3">
                        <div class="col-12">
                            <strong>Notes:</strong>
                            <p class="text-muted">{{ $assurance->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Statut Alert -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-{{ $assurance->alert_type }}">
                    <h5 class="mb-0 text-{{ $assurance->alert_type === 'warning' ? 'dark' : 'white' }}">
                        <i class="fas fa-exclamation-circle me-2"></i>Statut
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="display-6 mb-2">
                        {{ $assurance->alert_message }}
                    </div>
                    @if(now()->diffInDays($assurance->date_fin, false) <= 0)
                        <span class="badge bg-danger">⏰ Expirée</span>
                    @elseif(now()->diffInDays($assurance->date_fin, false) <= 30)
                        <span class="badge bg-warning">⚠️ À renouveler bientôt</span>
                    @else
                        <span class="badge bg-success">✓ OK</span>
                    @endif
                </div>
            </div>

            <!-- Informations -->
            <div class="card bg-light shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations</h5>
                </div>
                <div class="card-body small">
                    <p><strong>Créé le:</strong><br>{{ $assurance->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Modifié le:</strong><br>{{ $assurance->updated_at->format('d/m/Y H:i') }}</p>
                    <hr>
                    <p><strong>Action rapide:</strong><br>
                        <form action="{{ route('materiel.assurances.destroy', $assurance) }}" method="POST" onsubmit="return confirm('Supprimer cette assurance ?');" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                <i class="fas fa-trash me-1"></i>Supprimer
                            </button>
                        </form>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
