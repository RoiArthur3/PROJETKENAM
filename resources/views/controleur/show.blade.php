@extends('layouts.app')

@section('title', 'Détails du Contrôle - KENAM SERVICES')

@push('styles')
<style>
    .info-card {
        border-left: 4px solid #4e73df;
        transition: all 0.3s;
    }
    .info-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .badge {
        font-size: 0.9em;
        padding: 0.5em 0.8em;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Détails du Contrôle #{{ str_pad($controle->id, 6, '0', STR_PAD_LEFT) }}</h1>
        <div>
            <a href="{{ route('controleur.index') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i> Retour à la liste
            </a>
            <a href="{{ route('controleur.edit', $controle->id) }}" class="btn btn-primary me-2">
                <i class="fas fa-edit me-1"></i> Modifier
            </a>
            <form action="{{ route('controleur.destroy', $controle->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce contrôle ?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash me-1"></i> Supprimer
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Informations du contrôle</h6>
                    @php
                        $badgeClass = [
                            'conforme' => 'bg-success',
                            'non_conforme' => 'bg-danger',
                            'en_attente' => 'bg-warning'
                        ][$controle->resultat] ?? 'bg-secondary';
                        
                        $resultatText = [
                            'conforme' => 'Conforme',
                            'non_conforme' => 'Non Conforme',
                            'en_attente' => 'En Attente'
                        ][$controle->resultat] ?? 'Inconnu';
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ $resultatText }}</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Détails du véhicule</h5>
                            <p>
                                <strong>Immatriculation:</strong> {{ $controle->vehicle->immatriculation }}<br>
                                <strong>Marque/Modèle:</strong> {{ $controle->vehicle->marque }} {{ $controle->vehicle->modele }}<br>
                                <strong>Année:</strong> {{ $controle->vehicle->annee }}<br>
                                <strong>Kilométrage actuel:</strong> {{ number_format($controle->vehicle->kilometrage, 0, ',', ' ') }} km
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Détails du contrôle</h5>
                            <p>
                                <strong>Type de contrôle:</strong> {{ ucfirst($controle->type_controle) }}<br>
                                <strong>Date du contrôle:</strong> {{ \Carbon\Carbon::parse($controle->date_controle)->format('d/m/Y') }}<br>
                                <strong>Réalisé par:</strong> {{ $controle->user->name }}<br>
                                <strong>Date de création:</strong> {{ $controle->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>

                    @if($controle->commentaire)
                    <div class="mt-4">
                        <h5>Commentaires</h5>
                        <div class="border rounded p-3 bg-light">
                            {!! nl2br(e($controle->commentaire)) !!}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actions rapides</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('vehicules.show', $controle->vehicle_id) }}" class="btn btn-outline-primary text-start">
                            <i class="fas fa-car me-2"></i> Voir la fiche du véhicule
                        </a>
                        <a href="#" class="btn btn-outline-success text-start">
                            <i class="fas fa-print me-2"></i> Imprimer le rapport
                        </a>
                        <a href="#" class="btn btn-outline-info text-start">
                            <i class="fas fa-envelope me-2"></i> Envoyer par email
                        </a>
                    </div>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Historique des contrôles</h6>
                </div>
                <div class="card-body">
                    @php
                        $historique = \App\Models\Controle::where('vehicule_id', $controle->vehicle_id)
                            ->where('id', '!=', $controle->id)
                            ->orderBy('date_controle', 'desc')
                            ->take(5)
                            ->get();
                    @endphp

                    @if($historique->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($historique as $item)
                                <a href="{{ route('controleur.show', $item->id) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ ucfirst($item->type_controle) }}</h6>
                                        <small>{{ $item->date_controle->format('d/m/Y') }}</small>
                                    </div>
                                    <p class="mb-1">
                                        @php
                                            $badgeClass = [
                                                'conforme' => 'bg-success',
                                                'non_conforme' => 'bg-danger',
                                                'en_attente' => 'bg-warning'
                                            ][$item->resultat] ?? 'bg-secondary';
                                            
                                            $resultatText = [
                                                'conforme' => 'Conforme',
                                                'non_conforme' => 'Non Conforme',
                                                'en_attente' => 'En Attente'
                                            ][$item->resultat] ?? 'Inconnu';
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $resultatText }}</span>
                                    </p>
                                    @if($item->commentaire)
                                        <small class="text-muted">{{ Str::limit($item->commentaire, 50) }}</small>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                        <div class="mt-3 text-center">
                            <a href="{{ route('controleur.index', ['vehicule_id' => $controle->vehicle_id]) }}" class="btn btn-sm btn-outline-primary">
                                Voir tout l'historique
                            </a>
                        </div>
                    @else
                        <div class="text-center py-3 text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p class="mb-0">Aucun autre contrôle enregistré pour ce véhicule</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@if($controle->statut_soumission === 'brouillon')
    @include('controleur.partials.submit-to-services-form')
@else
    <div class="card mt-4">
        <div class="card-header {{ $controle->statut_soumission === 'soumis' ? 'bg-info' : ($controle->statut_soumission === 'traite' ? 'bg-success' : 'bg-warning') }} text-white">
            <h5 class="mb-0">Statut de soumission</h5>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge {{ $controle->statut_soumission === 'soumis' ? 'bg-info' : ($controle->statut_soumission === 'traite' ? 'bg-success' : 'bg-warning') }}">
                        {{ $controle::STATUTS[$controle->statut_soumission] ?? $controle->statut_soumission }}
                    </span>
                    @if($controle->soumis_le)
                        <div class="text-muted small mt-1">Soumis le: {{ $controle->soumis_le->format('d/m/Y H:i') }}</div>
                    @endif
                    @if($controle->commentaire_soumission)
                        <div class="mt-2">
                            <strong>Commentaire :</strong>
                            <p class="mb-0">{{ $controle->commentaire_soumission }}</p>
                        </div>
                    @endif
                </div>
                
                @if($controle->services->isNotEmpty())
                    <div class="text-end">
                        <h6>Services notifiés :</h6>
                        <ul class="list-unstyled mb-0">
                            @foreach($controle->services as $service)
                                <li>
                                    {{ $service->nom }}
                                    @if($service->pivot->statut === 'en_attente')
                                        <span class="badge bg-warning">En attente</span>
                                    @elseif($service->pivot->statut === 'traite')
                                        <span class="badge bg-success">Traité</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($service->pivot->statut) }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script>
// Scripts spécifiques à la page de détail
$(document).ready(function() {
    // Initialisation des tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Gestion de la confirmation de suppression
    $('form[onsubmit]').on('submit', function(e) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer ce contrôle ? Cette action est irréversible.')) {
            e.preventDefault();
            return false;
        }
        return true;
    });
});
</script>
@endpush
