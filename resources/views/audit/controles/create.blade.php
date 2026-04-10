@extends('layouts.app')

@section('title', 'Nouveau Contrôle - KENAM SERVICES')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Nouveau Contrôle d'Audit</h5>
                    <a href="{{ route('audit.controles') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('audit.controles.store') }}">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="type" class="form-label">Type de contrôle *</label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">Sélectionner un type</option>
                                    <option value="Interne" {{ old('type') == 'Interne' ? 'selected' : '' }}>Interne</option>
                                    <option value="Externe" {{ old('type') == 'Externe' ? 'selected' : '' }}>Externe</option>
                                    <option value="Conformité" {{ old('type') == 'Conformité' ? 'selected' : '' }}>Conformité</option>
                                    <option value="Processus" {{ old('type') == 'Processus' ? 'selected' : '' }}>Processus</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="departement" class="form-label">Département *</label>
                                <select class="form-select @error('departement') is-invalid @enderror" id="departement" name="departement" required>
                                    <option value="">Sélectionner un département</option>
                                    <option value="Finance" {{ old('departement') == 'Finance' ? 'selected' : '' }}>Finance</option>
                                    <option value="Ressources Humaines" {{ old('departement') == 'Ressources Humaines' ? 'selected' : '' }}>Ressources Humaines</option>
                                    <option value="Informatique" {{ old('departement') == 'Informatique' ? 'selected' : '' }}>Informatique</option>
                                    <option value="Logistique" {{ old('departement') == 'Logistique' ? 'selected' : '' }}>Logistique</option>
                                    <option value="Commercial" {{ old('departement') == 'Commercial' ? 'selected' : '' }}>Commercial</option>
                                    <option value="Production" {{ old('departement') == 'Production' ? 'selected' : '' }}>Production</option>
                                </select>
                                @error('departement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="date_debut" class="form-label">Date de début *</label>
                                <input type="date" class="form-control @error('date_debut') is-invalid @enderror" id="date_debut" name="date_debut" value="{{ old('date_debut', now()->format('Y-m-d')) }}" required>
                                @error('date_debut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="date_fin" class="form-label">Date de fin prévue *</label>
                                <input type="date" class="form-control @error('date_fin') is-invalid @enderror" id="date_fin" name="date_fin" value="{{ old('date_fin', now()->addDays(7)->format('Y-m-d')) }}" required>
                                @error('date_fin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="auditeur" class="form-label">Auditeur responsable *</label>
                                <input type="text" class="form-control @error('auditeur') is-invalid @enderror" id="auditeur" name="auditeur" value="{{ old('auditeur') }}" required>
                                @error('auditeur')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="equipe" class="form-label">Équipe d'audit</label>
                                <input type="text" class="form-control @error('equipe') is-invalid @enderror" id="equipe" name="equipe" value="{{ old('equipe') }}">
                                @error('equipe')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="objectif" class="form-label">Objectif du contrôle *</label>
                            <textarea class="form-control @error('objectif') is-invalid @enderror" id="objectif" name="objectif" rows="2" required>{{ old('objectif') }}</textarea>
                            @error('objectif')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description détaillée</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="document_reference" class="form-label">Documents de référence</label>
                            <input type="file" class="form-control @error('document_reference') is-invalid @enderror" id="document_reference" name="document_reference">
                            <small class="text-muted">Formats acceptés : PDF, DOC, DOCX, XLS, XLSX (max: 5MB)</small>
                            @error('document_reference')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-undo"></i> Réinitialiser
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer le contrôle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
