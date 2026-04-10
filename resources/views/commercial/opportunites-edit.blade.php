@extends('layouts.app')

@section('title', 'Éditer Opportunité - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Éditer l'opportunité</h1>
        <a href="{{ route('commercial.opportunites.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour à la liste
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('commercial.opportunites.update', $opportunite) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Titre de l'opportunité *</label>
                        <input type="text" class="form-control" name="titre" value="{{ old('titre', $opportunite->titre) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Client/Prospect *</label>
                        <select class="form-select" name="client_id" required>
                            <option value="">Sélectionner...</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" @selected(old('client_id', $opportunite->client_id) == $client->id)>
                                    {{ $client->nom ?? $client->raison_sociale ?? $client->id }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Montant estimé (FCFA) *</label>
                        <input type="number" class="form-control" name="montant" value="{{ old('montant', $opportunite->montant) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Statut *</label>
                        <select class="form-select" name="statut" required>
                            <option value="">Sélectionner...</option>
                            @foreach(['identification','proposition','négociation','gagné','perdu'] as $statut)
                                <option value="{{ $statut }}" @selected(old('statut', $opportunite->statut) === $statut)>
                                    {{ ucfirst($statut) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Probabilité (%) *</label>
                        <input type="number" class="form-control" name="probabilite" min="0" max="100" value="{{ old('probabilite', $opportunite->probabilite) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date d'échéance *</label>
                        <input type="date" class="form-control" name="date_echeance" value="{{ old('date_echeance', optional($opportunite->date_echeance)->format('Y-m-d')) }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3">{{ old('description', $opportunite->description) }}</textarea>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
