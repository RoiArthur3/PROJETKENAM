@extends('layouts.app')

@section('title', 'Modifier un Projet - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-edit me-2 text-primary"></i>Modifier le Projet
            </h1>
            <p class="text-muted mb-0">{{ $projet->numero_projet }}</p>
        </div>
        <a href="{{ route('projets.show', $projet) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour au détail
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 fw-bold">
                        <i class="fas fa-info-circle me-2"></i>Informations du Projet
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('projets.update', $projet) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="form-label fw-bold">Client</label>
                            <select class="form-select" name="client_id">
                                <option value="">-- Sélectionner un client --</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ (old('client_id', $projet->client_id) == $client->id) ? 'selected' : '' }}>
                                        {{ $client->nom ?? $client->name ?? ('Client #'.$client->id) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nom du Projet *</label>
                                <input type="text" class="form-control" name="titre" value="{{ old('titre', $projet->titre) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Date de Fin Prévue</label>
                                <input type="date" class="form-control" name="echeance" value="{{ old('echeance', optional($projet->echeance)->format('Y-m-d')) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Type de Prestation</label>
                                <select class="form-select" name="service">
                                    @php $service = old('service', $projet->service); @endphp
                                    <option value="">-- Sélectionner --</option>
                                    <option value="Transport de marchandises" {{ $service=='Transport de marchandises' ? 'selected' : '' }}>Transport de marchandises</option>
                                    <option value="Transport frigorifique" {{ $service=='Transport frigorifique' ? 'selected' : '' }}>Transport frigorifique</option>
                                    <option value="Logistique & Stockage" {{ $service=='Logistique & Stockage' ? 'selected' : '' }}>Logistique & Stockage</option>
                                    <option value="Transport multimodal" {{ $service=='Transport multimodal' ? 'selected' : '' }}>Transport multimodal</option>
                                    <option value="Distribution" {{ $service=='Distribution' ? 'selected' : '' }}>Distribution</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Priorité</label>
                                @php $priorite = old('priorite', $projet->priorite); @endphp
                                <select class="form-select" name="priorite">
                                    <option value="">-- Sélectionner --</option>
                                    <option value="moyenne" {{ $priorite=='moyenne' ? 'selected' : '' }}>Normale</option>
                                    <option value="haute" {{ $priorite=='haute' ? 'selected' : '' }}>Haute</option>
                                    <option value="basse" {{ $priorite=='basse' ? 'selected' : '' }}>Basse</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Description du Projet</label>
                                <textarea class="form-control" name="description" rows="3">{{ old('description', $projet->description) }}</textarea>
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="m-0 fw-bold">
                        <i class="fas fa-user-tie me-2"></i>Responsable
                    </h6>
                </div>
                <div class="card-body">
                    <label class="form-label fw-bold">Chef de Projet</label>
                    <select class="form-select" form="">
                        <option selected>{{ $projet->responsable_name ?? 'Non défini' }}</option>
                    </select>
                    <small class="text-muted d-block mt-2">Pour l'instant, le responsable est édité dans le bloc principal.</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
