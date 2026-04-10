@extends('layouts.app')

@section('title', 'Nouveau Contrat | KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-file-contract me-2"></i>
                        Nouveau Contrat
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('commercial.dashboard') }}">Commercial</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('commercial.contrats.index') }}">Contrats</a>
                        </li>
                        <li class="breadcrumb-item active">Nouveau</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <form method="POST" action="{{ route('commercial.contrats.store') }}">
                @csrf
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-file-contract me-2"></i>
                                    Informations du contrat
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Client -->
                                    <div class="col-md-6">
                                        <label for="client_id" class="form-label">Client *</label>
                                        <select name="client_id" id="client_id" class="form-select @error('client_id') is-invalid @enderror" required>
                                            <option value="">Sélectionner un client</option>
                                            @if(isset($clients))
                                                @foreach($clients as $client)
                                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id || request('client_id') == $client->id ? 'selected' : '' }}>
                                                        {{ $client->raison_sociale ?? $client->nom }} ({{ $client->email }})
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('client_id')
                                            <div class="invalid-feedback d-block">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Service -->
                                    <div class="col-md-6">
                                        <label for="service_id" class="form-label">Service *</label>
                                        <select name="service_id" id="service_id" class="form-select @error('service_id') is-invalid @enderror" required>
                                            <option value="">Sélectionner un service</option>
                                            @if(isset($services))
                                                @foreach($services as $service)
                                                    <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                                        {{ $service->nom }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('service_id')
                                            <div class="invalid-feedback d-block">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Numéro -->
                                    <div class="col-md-6">
                                        <label for="numero" class="form-label">Numéro du contrat *</label>
                                        <input type="text" name="numero" id="numero" class="form-control @error('numero') is-invalid @enderror"
                                               value="{{ old('numero') }}" placeholder="Ex: CONT-2025-001" required>
                                        @error('numero')
                                            <div class="invalid-feedback d-block">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Type -->
                                    <div class="col-md-6">
                                        <label for="type" class="form-label">Type de contrat *</label>
                                        <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
                                            <option value="">Sélectionner un type</option>
                                            <option value="maintenance" {{ old('type') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                            <option value="prestation" {{ old('type') == 'prestation' ? 'selected' : '' }}>Prestation de service</option>
                                            <option value="location" {{ old('type') == 'location' ? 'selected' : '' }}>Location</option>
                                            <option value="vente" {{ old('type') == 'vente' ? 'selected' : '' }}>Vente</option>
                                            <option value="autre" {{ old('type') == 'autre' ? 'selected' : '' }}>Autre</option>
                                        </select>
                                        @error('type')
                                            <div class="invalid-feedback d-block">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Montant -->
                                    <div class="col-md-4">
                                        <label for="montant_ht" class="form-label">Montant HT (FCFA) *</label>
                                        <input type="number" name="montant_ht" id="montant_ht" class="form-control @error('montant_ht') is-invalid @enderror"
                                               value="{{ old('montant_ht') }}" step="0.01" min="0" placeholder="0.00" required>
                                        @error('montant_ht')
                                            <div class="invalid-feedback d-block">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- TVA -->
                                    <div class="col-md-4">
                                        <label for="tva" class="form-label">TVA (%) *</label>
                                        <input type="number" name="tva" id="tva" class="form-control @error('tva') is-invalid @enderror"
                                               value="{{ old('tva', 20) }}" step="0.1" min="0" max="100" placeholder="20.0" required>
                                        @error('tva')
                                            <div class="invalid-feedback d-block">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Montant TTC (calculé) -->
                                    <div class="col-md-4">
                                        <label for="montant_ttc" class="form-label">Montant TTC (FCFA)</label>
                                        <input type="number" name="montant_ttc" id="montant_ttc" class="form-control"
                                               readonly step="0.01" placeholder="0.00">
                                        <small class="form-text text-muted">Calculé automatiquement</small>
                                    </div>

                                    <!-- Dates -->
                                    <div class="col-md-4">
                                        <label for="date_debut" class="form-label">Date de début *</label>
                                        <input type="date" name="date_debut" id="date_debut" class="form-control @error('date_debut') is-invalid @enderror"
                                               value="{{ old('date_debut') }}" required>
                                        @error('date_debut')
                                            <div class="invalid-feedback d-block">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="date_fin" class="form-label">Date de fin *</label>
                                        <input type="date" name="date_fin" id="date_fin" class="form-control @error('date_fin') is-invalid @enderror"
                                               value="{{ old('date_fin') }}" required>
                                        @error('date_fin')
                                            <div class="invalid-feedback d-block">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="statut" class="form-label">Statut *</label>
                                        <select id="statut" name="statut" class="form-select @error('statut') is-invalid @enderror" required>
                                            <option value="brouillon" {{ old('statut', 'brouillon') == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                                            <option value="en_attente" {{ old('statut') == 'en_attente' ? 'selected' : '' }}>En attente de signature</option>
                                            <option value="actif" {{ old('statut') == 'actif' ? 'selected' : '' }}>Actif</option>
                                            <option value="termine" {{ old('statut') == 'termine' ? 'selected' : '' }}>Terminé</option>
                                            <option value="annule" {{ old('statut') == 'annule' ? 'selected' : '' }}>Annulé</option>
                                        </select>
                                        @error('statut')
                                            <div class="invalid-feedback d-block">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Renouvellement -->
                                    <div class="col-md-6">
                                        <label for="renouvellement" class="form-label">Renouvellement</label>
                                        <select id="renouvellement" name="renouvellement" class="form-select @error('renouvellement') is-invalid @enderror">
                                            <option value="manuel" {{ old('renouvellement', 'manuel') == 'manuel' ? 'selected' : '' }}>Manuel</option>
                                            <option value="automatique" {{ old('renouvellement') == 'automatique' ? 'selected' : '' }}>Automatique</option>
                                        </select>
                                        @error('renouvellement')
                                            <div class="invalid-feedback d-block">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Description -->
                                    <div class="col-12">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Description détaillée du contrat...">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback d-block">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Conditions -->
                                    <div class="col-12">
                                        <label for="conditions" class="form-label">Conditions particulières</label>
                                        <textarea id="conditions" name="conditions" class="form-control @error('conditions') is-invalid @enderror" rows="3" placeholder="Conditions particulières du contrat...">{{ old('conditions') }}</textarea>
                                        @error('conditions')
                                            <div class="invalid-feedback d-block">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="m-0">
                                    <i class="fas fa-cogs me-2"></i>Actions
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Enregistrer le contrat
                                    </button>
                                    <a href="{{ route('commercial.contrats.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>Annuler
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- KPIs -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6 class="m-0">
                                    <i class="fas fa-chart-bar me-2"></i>Statistiques
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <h4 class="text-primary">{{ DB::table('contrats')->count() }}</h4>
                                        <small class="text-muted">Total contrats</small>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="text-success">{{ DB::table('contrats')->where('statut', 'actif')->count() }}</h4>
                                        <small class="text-muted">Actifs</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const montantHt = document.getElementById('montant_ht');
    const tva = document.getElementById('tva');
    const montantTtc = document.getElementById('montant_ttc');

    function calculerTtc() {
        const ht = parseFloat(montantHt.value) || 0;
        const tauxTva = parseFloat(tva.value) || 0;
        const ttc = ht * (1 + tauxTva / 100);
        montantTtc.value = ttc.toFixed(2);
    }

    montantHt.addEventListener('input', calculerTtc);
    tva.addEventListener('input', calculerTtc);

    // Calcul initial
    calculerTtc();
});
</script>
@endpush
@endsection
