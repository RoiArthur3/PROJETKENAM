@extends('layouts.app')

@section('title', 'Test Intégration Services | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-cogs me-2 text-primary"></i>Test d'Intégration des Services
        </h1>
        <a href="{{ route('services.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour aux Services
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-envelope me-2"></i>Formulaire de Test - Envoi d'Email
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('services.email.send') }}">
                        @csrf
                        
                        <!-- Utilisation du composant de sélecteur de services -->
                        <div class="mb-4">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-users me-2"></i>Sélection des Destinataires
                            </h6>
                            
                            <!-- Sélecteur simple -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Destinataire principal</label>
                                <select name="services[]" class="form-select" required>
                                    <option value="">Choisir un service...</option>
                                    @foreach(App\Models\ServiceOperationnel::where('actif', true)->whereNotNull('email')->get() as $service)
                                        <option value="{{ $service->id }}">
                                            {{ $service->nom }} - {{ $service->email }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Sélecteur multiple pour CC -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Destinataires en CC</label>
                                <select name="services[]" class="form-select" multiple>
                                    <option value="">Choisir des services...</option>
                                    @foreach(App\Models\ServiceOperationnel::where('actif', true)->whereNotNull('email')->get() as $service)
                                        <option value="{{ $service->id }}">
                                            {{ $service->nom }} - {{ $service->email }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">
                                    Maintenez Ctrl (ou Cmd) pour sélectionner plusieurs services
                                </small>
                            </div>
                        </div>

                        <!-- Sujet et message -->
                        <div class="mb-3">
                            <label for="subject" class="form-label fw-bold">Sujet *</label>
                            <input type="text" class="form-control" id="subject" name="subject" 
                                   placeholder="Test d'intégration des services" required>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label fw-bold">Message *</label>
                            <textarea class="form-control" id="message" name="message" rows="6" required>
Ceci est un test d'intégration des services opérationnels.

Les services ci-dessous ont été créés via la nouvelle interface et utilisent de vraies adresses email enregistrées en base:

- Services avec vrais emails
- Intégration dans les formulaires d'opérations
- Système de validation par ordre

Cordialement,
L'équipe KENAM SERVICES
                            </textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('services.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-paper-plane me-2"></i>Tester l'Envoi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Exemple d'intégration dans un formulaire d'opération -->
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>Exemple: Circuit de Validation
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Voici comment les services peuvent être intégrés dans un circuit de validation pour les opérations:
                    </p>
                    
                    @php
                        $sampleServices = App\Models\ServiceOperationnel::where('actif', true)->whereNotNull('email')->take(3)->get();
                    @endphp
                    
                    <x-operation-service-selector :services="$sampleServices" />
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Informations sur les services -->
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-database me-2"></i>Services en Base
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-primary">Statistiques</h6>
                        <div class="row text-center">
                            <div class="col-6">
                                <h4 class="text-primary">{{ App\Models\ServiceOperationnel::count() }}</h4>
                                <small class="text-muted">Total</small>
                            </div>
                            <div class="col-6">
                                <h4 class="text-success">{{ App\Models\ServiceOperationnel::where('actif', true)->whereNotNull('email')->count() }}</h4>
                                <small class="text-muted">Actifs avec email</small>
                            </div>
                        </div>
                    </div>

                    <h6 class="text-primary mb-3">Services disponibles</h6>
                    <div class="list-group list-group-flush">
                        @foreach(App\Models\ServiceOperationnel::where('actif', true)->whereNotNull('email')->take(5)->get() as $service)
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-building me-1 text-primary"></i>
                                        <strong>{{ $service->nom }}</strong>
                                    </div>
                                    <small class="text-muted">{{ $service->email }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Guide d'utilisation -->
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0">
                        <i class="fas fa-book me-2"></i>Guide d'Utilisation
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="text-warning">Intégration dans les formulaires</h6>
                    <ul class="small">
                        <li>Utiliser <code>&lt;x-service-selector /&gt;</code> pour un sélecteur simple</li>
                        <li>Utiliser <code>&lt;x-operation-service-selector /&gt;</code> pour les circuits de validation</li>
                        <li>Les emails sont récupérés automatiquement de la base</li>
                        <li>Pas de fausses données - vrais services enregistrés</li>
                    </ul>

                    <h6 class="text-warning mt-3">Avantages</h6>
                    <ul class="small">
                        <li>✅ Données réelles en base</li>
                        <li>✅ Emails fonctionnels</li>
                        <li>✅ Gestion des permissions</li>
                        <li>✅ Circuit de validation</li>
                        <li>✅ Interface moderne</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Inclure les bibliothèques nécessaires -->
@if(isset($component) && str_contains($component, 'operation-service-selector'))
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endif
@endsection
