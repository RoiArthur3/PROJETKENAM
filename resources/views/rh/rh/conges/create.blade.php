@extends('layouts.app')

@section('title', 'Demande de Congé - KENAM SERVICES')

@section('content')
@php
    $colors = ['#4e73df','#1cc88a','#f6c23e','#36b9cc','#e74a3b','#858796','#5a5c69','#fd7e14'];
@endphp
<x-dashboard-layout title="Nouvelle Demande de Congé" icon="fa-umbrella-beach" subtitle="Créer une demande de congé pour le personnel RH">
    <!-- Formulaire de demande -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow-sm">
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('rh.conges.store') }}" id="congeForm">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-user me-2"></i>Informations du demandeur
                                </h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="personnel_id" class="form-label required">Personnel RH</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-users-cog"></i></span>
                                    <select name="personnel_id" id="personnel_id"
                                           class="form-select @error('personnel_id') is-invalid @enderror"
                                           required>
                                        <option value="">-- Sélectionner un employé RH --</option>
                                        @if(isset($personnels))
                                            @foreach($personnels as $personnel)
                                                <option value="{{ $personnel->id }}"
                                                        {{ old('personnel_id') == $personnel->id ? 'selected' : '' }}>
                                                    {{ $personnel->nom }} {{ $personnel->prenoms }} - {{ $personnel->poste }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                @error('personnel_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="type_conge" class="form-label required">Type de congé</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    <select name="type_conge" id="type_conge"
                                            class="form-select @error('type_conge') is-invalid @enderror" required>
                                        <option value="">-- Sélectionner --</option>
                                        <option value="Congé annuel" {{ old('type_conge') == 'Congé annuel' ? 'selected' : '' }}>Congé annuel</option>
                                        <option value="Congé maladie" {{ old('type_conge') == 'Congé maladie' ? 'selected' : '' }}>Congé maladie</option>
                                        <option value="Congé maternité" {{ old('type_conge') == 'Congé maternité' ? 'selected' : '' }}>Congé maternité</option>
                                        <option value="Congé sans solde" {{ old('type_conge') == 'Congé sans solde' ? 'selected' : '' }}>Congé sans solde</option>
                                        <option value="Permission" {{ old('type_conge') == 'Permission' ? 'selected' : '' }}>Permission</option>
                                        <option value="Autre" {{ old('type_conge') == 'Autre' ? 'selected' : '' }}>Autre</option>
                                    </select>
                                </div>
                                @error('type_conge')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-calendar me-2"></i>Période demandée
                                </h5>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="date_debut" class="form-label required">Date de début</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="far fa-calendar"></i></span>
                                    <input type="date" name="date_debut" id="date_debut"
                                           class="form-control @error('date_debut') is-invalid @enderror"
                                           value="{{ old('date_debut') }}"
                                           min="{{ date('Y-m-d') }}"
                                           required
                                           onchange="calculerJours()">
                                </div>
                                @error('date_debut')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="date_fin" class="form-label required">Date de fin</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="far fa-calendar"></i></span>
                                    <input type="date" name="date_fin" id="date_fin"
                                           class="form-control @error('date_fin') is-invalid @enderror"
                                           value="{{ old('date_fin') }}"
                                           min="{{ date('Y-m-d') }}"
                                           required
                                           onchange="calculerJours()">
                                </div>
                                @error('date_fin')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="nombre_jours_display" class="form-label">Nombre de jours</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                                    <input type="text" id="nombre_jours_display"
                                           class="form-control bg-light fw-bold"
                                           readonly
                                           value="0">
                                    <span class="input-group-text">jour(s)</span>
                                </div>
                                <small class="form-text">Calculé automatiquement</small>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-comment me-2"></i>Motif de la demande
                                </h5>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="motif" class="form-label">Motif (optionnel)</label>
                                <textarea name="motif" id="motif" rows="4"
                                          class="form-control @error('motif') is-invalid @enderror"
                                          placeholder="Précisez le motif de votre demande de congé...">{{ old('motif') }}</textarea>
                                @error('motif')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text">Ce champ est optionnel mais recommandé pour faciliter la validation</small>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Information:</strong> Votre demande sera soumise avec le statut "En attente" et devra être validée par votre responsable.
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-5 pt-3 border-top">
                            <a href="{{ route('rh.conges.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Soumettre la demande
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>

<script>
function calculerJours() {
    const dateDebut = document.getElementById('date_debut').value;
    const dateFin = document.getElementById('date_fin').value;

    if (dateDebut && dateFin) {
        const debut = new Date(dateDebut);
        const fin = new Date(dateFin);

        if (fin >= debut) {
            const diffTime = Math.abs(fin - debut);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            document.getElementById('nombre_jours_display').value = diffDays;
        } else {
            document.getElementById('nombre_jours_display').value = 0;
            alert('La date de fin doit être postérieure ou égale à la date de début');
        }
    }
}

// Calculer au chargement si valeurs présentes
document.addEventListener('DOMContentLoaded', function() {
    calculerJours();
});
</script>
@endsection
